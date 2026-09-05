<?php

namespace app\controllers;

use Yii;
use app\models\MonthlyInvoice;
use app\models\MonthlyInvoiceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;

/**
 * MonthlyInvoiceController implements the CRUD actions for MonthlyInvoice model.
 */
class MonthlyInvoiceController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index', 'view', 'create', 'update', 'delete', 'send-mail', 'generate', 'send-mail-to-all'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'send-mail', 'generate', 'send-mail-to-all'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_SUPER_ADMIN,
                            UserIdentity::ROLE_ADMIN,
                        ]
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'send-mail', 'generate'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_MODERATOR,
                        ]
                    ],
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_GENERAL_USER,
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all MonthlyInvoice models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new MonthlyInvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MonthlyInvoice model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new MonthlyInvoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new MonthlyInvoice();
        $model->currency_id = 13;
        $model->created_at = date('Y-m-d H:i:s');
        $model->updated_at = date('Y-m-d H:i:s');
        $model->monthly_invoice_number = \app\helpers\AppHelper::getNextMonthlyInvoiceNumber();
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            $check = MonthlyInvoice::find()
                    ->where([
                        'receiver_id' => $request['MonthlyInvoice']['receiver_id'],
                        'instalment_month' => $request['MonthlyInvoice']['instalment_month'],
                        'instalment_year' => $request['MonthlyInvoice']['instalment_year'],
                    ])
                    ->one();
            if (!empty($check)) {
                Yii::$app->session->setFlash('warning', $request['MonthlyInvoice']['instalment_month'] . ' month invoice already exist for this user');
                return $this->redirect(['create']);
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Invoice successfully added');
                $msg = 'Invoice#' . $model->monthly_invoice_number . ' generated for ' . $model->instalment_month . ' ' . $model->instalment_year . ' against receiver ' . $model->receiver->fullname . '. Created by ' . Yii::$app->user->identity->fullname;
                \app\helpers\AppHelper::addActivity("MI", $model->monthly_invoice_id, $msg);
                //
                if ($model->is_paid == 1 && $model->invoice_received_by != null && $model->invoice_received_date != null) {
                    $paymentReceived = new \app\models\PaymentReceived();
                    $paymentReceived->received_invoice_number = \app\helpers\AppHelper::getReceivePayInvoiceNumber();
                    $paymentReceived->donated_by = $model->receiver_id;
                    $paymentReceived->received_by = $model->invoice_received_by;
                    $paymentReceived->amount = $model->amount;
                    $paymentReceived->currency_id = $model->currency_id;
                    $paymentReceived->instalment_month = $model->instalment_month;
                    $paymentReceived->instalment_year = $model->instalment_year;
                    $paymentReceived->has_invoice = 1;
                    $paymentReceived->monthly_invoice_id = $model->monthly_invoice_id;
                    $paymentReceived->received_date = $model->invoice_received_date;
                    $paymentReceived->created_at = date('Y-m-d H:i:s');
                    $paymentReceived->updated_at = date('Y-m-d H:i:s');
                    if ($paymentReceived->save()) {
                        $msg1 = 'Invoice#' . $paymentReceived->received_invoice_number . ' generated for ' . $paymentReceived->instalment_month . ' ' . $paymentReceived->instalment_year . ' Donated By ' . $paymentReceived->donatedBy->fullname . '. Created by ' . Yii::$app->user->identity->fullname;
                        \app\helpers\AppHelper::addActivity("PREC", $paymentReceived->payment_received_id, $msg1);
                        //
                        /* Yii::$app->mailer->compose('@app/mail/receive-invoice-mail', [
                          'model' => $paymentReceived,
                          ])
                          ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                          ->setTo($paymentReceived->donatedBy->email)
                          ->setSubject("Confirmation of your BCF contribution (Invoice#" . $paymentReceived->received_invoice_number . ")")
                          ->send(); */
                        $subject = "Confirmation of your BCF contribution (Invoice#" . $paymentReceived->received_invoice_number . ")";
                        $mailDetails = "<p>
                                            Assalamu Alaikum,
                                        </p>
                                        <p>
                                            Dear Brother " . $paymentReceived->donatedBy->fullname . ",
                                        </p>

                                        <p>
                                            We do confirm your following contribution for BIMT Charity Foundation:
                                        </p>
                                        <p>
                                            Amount: " . $paymentReceived->amount . " " . $paymentReceived->currency->code . "<br/>
                                            Received Date: " . date('d.m.Y', strtotime($paymentReceived->created_at)) . "<br/>
                                            For Month(s): " . $paymentReceived->instalment_month . " " . $paymentReceived->instalment_year . "<br/>
                                            Comments: " . $paymentReceived->comments . "
                                        </p>

                                        <p>
                                            Your SADAKAH has been received with thanks. For details you can visit our web portal,<br/>
                                            your SADAKAH has been documented under ‘+ Receive’ Menu
                                        </p>
                                        <p>

                                            “কে আছে যে আল্লাহকে উত্তম ঋণ দিবে ? তাহলে  তিনি তা বহুগুনে তার জন্য বৃদ্ধি করবেন এবং তার জন্য উত্তম পুরস্কার রয়েছে।“  [সূরা হাদীদ ৫৭:১১]

                                        </p>

                                        <p>
                                            M’assalam<br/>
                                            Finance Control Board<br/>
                                            BIMT Charity Foundation<br/>
                                            Webportal Link: http://bimtcharity.org/site/login

                                        </p>";

                        $mailObject = [
                            'from' => "BIMT Charity Foundation<communication@bimtcharity.org>",
                            'to' => $paymentReceived->donatedBy->email,
                            'subject' => $subject,
                            'html' => $mailDetails,
                        ];
                        \app\helpers\AppHelper::resendEmail($mailObject);

                        $pushHelper = new \app\helpers\PushHelper();
                        $pushHelper->sendPush([
                            'title' => 'New Sadaqah Received',
                            'body' => 'A new payment ' . $paymentReceived->received_invoice_number . ' has been submitted.',
                            'screen' => 'sadaqah',
                            'id' => $paymentReceived->payment_received_id,
                        ]);
                    }
                }
                $pushHelper = new \app\helpers\PushHelper();
                $pushHelper->sendPushToUser($model->receiver_id, [
                    'title' => 'New Invoice',
                    'body' => 'Monthly invoice ' . $model->monthly_invoice_number . ' has been generated.',
                    'screen' => 'invoice',
                    'id' => $model->monthly_invoice_id,
                ]);

                return $this->redirect(['view', 'id' => $model->monthly_invoice_id]);
            } else {
                return $this->render('create', [
                            'model' => $model,
                ]);
            }
        }
        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing MonthlyInvoice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->updated_at = date('Y-m-d H:i:s');
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Invoice successfully updated');
                $msg = 'Invoice#' . $model->monthly_invoice_number . ' has been modified by ' . Yii::$app->user->identity->fullname;
                \app\helpers\AppHelper::addActivity("MI", $model->monthly_invoice_id, $msg);
                //
                if ($model->is_paid == 1 && $model->invoice_received_by != null && $model->invoice_received_date != null) {
                    $paymentReceived = new \app\models\PaymentReceived();
                    $paymentReceived->received_invoice_number = \app\helpers\AppHelper::getReceivePayInvoiceNumber();
                    $paymentReceived->donated_by = $model->receiver_id;
                    $paymentReceived->received_by = $model->invoice_received_by;
                    $paymentReceived->amount = $model->amount;
                    $paymentReceived->currency_id = $model->currency_id;
                    $paymentReceived->instalment_month = $model->instalment_month;
                    $paymentReceived->instalment_year = $model->instalment_year;
                    $paymentReceived->has_invoice = 1;
                    $paymentReceived->monthly_invoice_id = $model->monthly_invoice_id;
                    $paymentReceived->received_date = $model->invoice_received_date;
                    $paymentReceived->created_at = date('Y-m-d H:i:s');
                    $paymentReceived->updated_at = date('Y-m-d H:i:s');
                    if ($paymentReceived->save()) {
                        $msg1 = 'Invoice#' . $paymentReceived->received_invoice_number . ' generated for ' . $paymentReceived->instalment_month . ' ' . $paymentReceived->instalment_year . ' Donated By ' . $paymentReceived->donatedBy->fullname . '. Created by ' . Yii::$app->user->identity->fullname;
                        \app\helpers\AppHelper::addActivity("PREC", $paymentReceived->payment_received_id, $msg1);
                        //
                        /* Yii::$app->mailer->compose('@app/mail/receive-invoice-mail', [
                          'model' => $paymentReceived,
                          ])
                          ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                          ->setTo($paymentReceived->donatedBy->email)
                          ->setSubject("Confirmation of your BCF contribution (Invoice#" . $paymentReceived->received_invoice_number . ")")
                          ->send(); */
                        $subject = "Confirmation of your BCF contribution (Invoice#" . $paymentReceived->received_invoice_number . ")";
                        $mailDetails = "<p>
                                            Assalamu Alaikum,
                                        </p>
                                        <p>
                                            Dear Brother " . $paymentReceived->donatedBy->fullname . ",
                                        </p>

                                        <p>
                                            We do confirm your following contribution for BIMT Charity Foundation:
                                        </p>
                                        <p>
                                            Amount: " . $paymentReceived->amount . " " . $paymentReceived->currency->code . "<br/>
                                            Received Date: " . date('d.m.Y', strtotime($paymentReceived->created_at)) . "<br/>
                                            For Month(s): " . $paymentReceived->instalment_month . " " . $paymentReceived->instalment_year . "<br/>
                                            Comments: " . $paymentReceived->comments . "
                                        </p>

                                        <p>
                                            Your SADAKAH has been received with thanks. For details you can visit our web portal,<br/>
                                            your SADAKAH has been documented under ‘+ Receive’ Menu
                                        </p>
                                        <p>

                                            “কে আছে যে আল্লাহকে উত্তম ঋণ দিবে ? তাহলে  তিনি তা বহুগুনে তার জন্য বৃদ্ধি করবেন এবং তার জন্য উত্তম পুরস্কার রয়েছে।“  [সূরা হাদীদ ৫৭:১১]

                                        </p>

                                        <p>
                                            M’assalam<br/>
                                            Finance Control Board<br/>
                                            BIMT Charity Foundation<br/>
                                            Webportal Link: http://bimtcharity.org/site/login

                                        </p>";

                        $mailObject = [
                            'from' => "BIMT Charity Foundation<communication@bimtcharity.org>",
                            'to' => $paymentReceived->donatedBy->email,
                            'subject' => $subject,
                            'html' => $mailDetails,
                        ];
                        \app\helpers\AppHelper::resendEmail($mailObject);

                        $pushHelper = new \app\helpers\PushHelper();
                        $pushHelper->sendPush([
                            'title' => 'New Sadaqah Received',
                            'body' => 'A new payment ' . $paymentReceived->received_invoice_number . ' has been submitted.',
                            'screen' => 'sadaqah',
                            'id' => $paymentReceived->payment_received_id,
                        ]);
                    }
                }
                $pushHelper = new \app\helpers\PushHelper();
                $pushHelper->sendPushToUser($model->receiver_id, [
                    'title' => 'Updated Invoice:' . $model->monthly_invoice_number,
                    'body' => 'Monthly invoice ' . $model->monthly_invoice_number . ' has been updated.',
                    'screen' => 'invoice',
                    'id' => $model->monthly_invoice_id,
                ]);
                return $this->redirect(['view', 'id' => $model->monthly_invoice_id]);
            } else {
                return $this->render('update', [
                            'model' => $model,
                ]);
            }
        }

        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing MonthlyInvoice model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        $model->save();
        $msg = 'Invoice#' . $model->monthly_invoice_number . ' has been deleted by ' . Yii::$app->user->identity->fullname;
        \app\helpers\AppHelper::addActivity("MI", $model->monthly_invoice_id, $msg);
        Yii::$app->session->setFlash('success', 'Invoice successfully deleted');
        return $this->redirect(['index']);
    }

    public function actionSendMail($id) {
        $model = $this->findModel($id);
        if ($model->is_paid != 1) {
            /* Yii::$app->mailer->compose('@app/mail/invoice-mail', [
              'model' => $model,
              ])
              ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
              ->setTo($model->receiver->email)
              ->setSubject("Your Sadakah for " . $model->instalment_month . " " . $model->instalment_year . '(Invoice#' . $model->monthly_invoice_number . ')')
              ->send(); */
            $subject = "Your Sadakah for " . $model->instalment_month . " " . $model->instalment_year . '(Invoice#' . $model->monthly_invoice_number . ')';

            $mailDetails = "<p>
                                Assalamualaikum,<br/>
                                Dear Brother, " . $model->receiver->fullname . "
                            </p>
                            <p>
                                We hope by the mercy of almighty Allah (SW) you are doing well as well as your family members.<br/> 
                                This is the beginning of " . $model->instalment_month . ". This is why we would like to cordially request you to contribute for 'BIMT Charity Foundation' with your SADAKAH.
                            </p>
                            <p>
                                Proposed amount: " . $model->amount . " " . $model->currency->code . " (more or less amount is unquestionably acceptable)<br/>
                                Proposed deadline: 15 " . $model->instalment_month . "," . $model->instalment_year . "
                            </p>
                            <p>
                                In case you need to mention a SUBJECT during Transfer, just write FOR BCF as subject. 
                                It is strongly recommended to inform corresponding account holder (mentioning your member ID, 
                                if possible) after transferring the money so that we can track your transaction.
                            </p>
                            <p>
                                Insha Allah we will try our level best to use your SADAKAH in right way. Verily, Allah is all knowing and all seeing.
                            </p>

                            <p>
                                Bank Details:
                            </p>
                            <p>
                                Bangladesh:<br/>
                                Account Holder Name: Mahmud Arafat Bin Rafiq<br/>
                                Account Number: 114.103.0383245<br/>
                                Bank Name: Dutch Bangla Bank Limited
                            </p>
                            <p>
                                Bikash: 008801719127039 (Mahbubur Rahman)
                            </p>
                            <p>
                                Germany:<br/>
                                Account Holder Name: MD Alif Khondokar<br/>
                                IBAN: DE78 1007 0024 0214 2370 00<br/>
                                BIC: DEUTDEDBBER
                            </p>
                            <p>
                                Singapore:<br/>
                                Account Holder Name: Mohin Md Rakibul Ahsun<br/>
                                Bank Name: POSB<br/>
                                Account Number: 248-85387-5 (Savings) 
                            </p>
                            <p>
                                May Allah accept our SADAKAH, our all efforts and make these a good reason go acquire Allah's satisfaction in Dunya and in Akhira.
                            </p>
                            <p>
                                M’assalam<br/>
                                Finance Control Board<br/>
                                BIMT Charity Foundation<br/>
                                For detail please contact with Rafiq Bin Arafat, Rakibul Ahsun Mohin, Alif Khondokar.<br/>
                                Web portal Link: http://bimtcharity.org/site/login
                            </p>";

            $mailObject = [
                'from' => "BIMT Charity Foundation<communication@bimtcharity.org>",
                'to' => $model->receiver->email,
                'subject' => $subject,
                'html' => $mailDetails,
            ];
            \app\helpers\AppHelper::resendEmail($mailObject);

            $pushHelper = new \app\helpers\PushHelper();
            $pushHelper->sendPushToUser($model->receiver_id, [
                'title' => 'New Invoice',
                'body' => $subject,
                'screen' => 'invoice',
                'id' => $model->monthly_invoice_id,
            ]);
        }
        Yii::$app->session->setFlash('success', 'Invoice successfully sent');
        return $this->redirect(['index']);
    }

    public function actionGenerate() {
        $users = \app\models\Users::find()
                ->where(['is_deleted' => 0, 'is_active' => 1])
                ->all();
        $proccessed = 0;
        if (!empty($users)) {
            $mailArr = [];
            foreach ($users as $user) {
                $model = MonthlyInvoice::find()
                        ->where(['receiver_id' => $user->user_id, 'instalment_month' => date('F'), 'instalment_year' => date('Y')])
                        ->one();
                if (empty($model)) {
                    if ($user->recurring_amount > 0) {
                        $model = new MonthlyInvoice();
                        $model->created_at = date('Y-m-d H:i:s');
                        $model->updated_at = date('Y-m-d H:i:s');
                        $model->monthly_invoice_number = \app\helpers\AppHelper::getNextMonthlyInvoiceNumber();
                        $model->receiver_id = $user->user_id;
                        $model->amount = $user->recurring_amount;
                        $model->currency_id = $user->currency_id;
                        $model->instalment_month = date('F');
                        $model->instalment_year = date('Y');
                        $model->is_paid = 0;
                        $model->is_deleted = 0;
                        if ($model->save()) {
                            $proccessed = 1;
                            $msg = 'Invoice#' . $model->monthly_invoice_number . ' generated for ' . $model->instalment_month . ' ' . $model->instalment_year . ' against receiver ' . $model->receiver->fullname . '. Created by ' . Yii::$app->user->identity->fullname;
                            \app\helpers\AppHelper::addActivity("MI", $model->monthly_invoice_id, $msg);

                            array_push($mailArr, $user->email);

                            $pushHelper = new \app\helpers\PushHelper();
                            $pushHelper->sendPushToUser($model->receiver_id, [
                                'title' => 'New Invoice',
                                'body' => 'Monthly invoice ' . $model->monthly_invoice_number . ' has been generated.',
                                'screen' => 'invoice',
                                'id' => $model->monthly_invoice_id,
                            ]);
                            /* Yii::$app->mailer->compose('@app/mail/invoice-mail', [
                              'model' => $model,
                              ])
                              ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                              ->setTo($model->receiver->email)
                              ->setSubject("Your Sadakah for " . $model->instalment_month . " " . $model->instalment_year . '(Invoice#' . $model->monthly_invoice_number . ')')
                              ->send(); */
                        } else {
                            die(json_encode($model->errors));
                        }
                    }
                }
            }
            /* Yii::$app->mailer->compose('@app/mail/invoice-mail-common', [])
              ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
              ->setTo($mailArr)
              ->setSubject("Your Sadakah for " . date('F') . " " . date('Y'))
              ->send(); */
        }
        if ($proccessed == 1) {
            Yii::$app->session->setFlash('success', 'Invoice successfully generated');
        } else {
            Yii::$app->session->setFlash('warning', 'No invoice generated');
        }
        return $this->redirect(['index']);
    }

    public function actionSendMailToAll() {
        $userCount = \app\models\Users::find()
                ->where(['is_deleted' => 0, 'is_active' => 1])
                ->count();
        $batchSize = 40;
        $totalBatches = ceil($userCount / $batchSize);
        $subject = "Your Sadakah for " . date('F') . " " . date('Y');
        $mailDetails = "<p>
                        Assalamualaikum,<br/>
                        Dear Brother,
                    </p>
                    <p>
                        We hope by the mercy of almighty Allah (SW) you are doing well as well as your family members.<br/> 
                        This is the beginning of " . date('F') . ". This is why we would like to cordially request you to contribute for 'BIMT Charity Foundation' with your SADAKAH.
                    </p>
                    <p>
                        Proposed amount: 200|500|1000 BDT|USD|EUR (more or less amount is unquestionably acceptable)<br/>
                        Proposed deadline: 15 " . date('F') . "," . date('Y') . "
                    </p>
                    <p>
                        In case you need to mention a SUBJECT during Transfer, just write FOR BCF as subject. 
                        It is strongly recommended to inform corresponding account holder (mentioning your member ID, 
                        if possible) after transferring the money so that we can track your transaction.
                    </p>
                    <p>
                        Insha Allah we will try our level best to use your SADAKAH in right way. Verily, Allah is all knowing and all seeing.
                    </p>

                    <p>
                        Bank Details:
                    </p>
                    <p>
                        Bikash: 008801719127039 (Mahbubur Rahman)
                    </p>
                    <p>
                        Germany:<br/>
                        Account Holder Name: MD Alif Khondokar<br/>
                        IBAN: DE78 1007 0024 0214 2370 00<br/>
                        BIC: DEUTDEDBBER
                    </p>
                    <p>
                        Singapore:<br/>
                        Account Holder Name: Mohin Md Rakibul Ahsun<br/>
                        Bank Name: POSB<br/>
                        Account Number: 248-85387-5 (Savings) 
                    </p>
                    <p>
                        May Allah accept our SADAKAH, our all efforts and make these a good reason go acquire Allah's satisfaction in Dunya and in Akhira.
                    </p>
                    <p>
                        M’assalam<br/>
                        Finance Control Board<br/>
                        BIMT Charity Foundation<br/>
                        For detail please contact with Rakibul Ahsun Mohin, Alif Khondokar.<br/>
                        Web portal Link: http://bimtcharity.org/site/login
                    </p>";
        for ($batch = 0; $batch < $totalBatches; $batch++) {
            $offset = $batch * $batchSize;
            $users = \app\models\Users::find()
                    ->where(['is_deleted' => 0, 'is_active' => 1])
                    ->offset($offset)
                    ->limit($batchSize)
                    ->all();
            $emailList = [];
            foreach ($users as $user) {

                $pushHelper = new \app\helpers\PushHelper();
                $pushHelper->sendPushToUser($user->receiver_id, [
                    'title' => 'New Invoice',
                    'body' => $subject,
                    'screen' => 'invoice',
                    'id' => "",
                ]);

                array_push($emailList, $user->email);
            }

            $mailObject = [
                'from' => "BIMT Charity Foundation<communication@bimtcharity.org>",
                'to' => $emailList,
                'subject' => $subject,
                'html' => $mailDetails,
            ];
            /* $mailObject = '{
              "from": "BIMT Charity Foundation<communication@bimtcharity.org>",
              "to": '. json_encode($emailList).',
              "subject": "'.$subject.'",
              "html": "'.$mailDetails.'"
              }'; */

            //debugPrint(json_encode($mailObject));
            \app\helpers\AppHelper::resendEmail($mailObject);
        }
        Yii::$app->session->setFlash('success', 'Mail successfully sent');
        return $this->redirect(['index']);
    }

    /**
     * Finds the MonthlyInvoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MonthlyInvoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = MonthlyInvoice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
