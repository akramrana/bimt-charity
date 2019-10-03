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
class MonthlyInvoiceController extends Controller
{

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
                'only' => ['index', 'view', 'create', 'update', 'delete', 'send-mail', 'generate'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'send-mail', 'generate'],
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
                    Yii::$app->mailer->compose('@app/mail/receive-invoice-mail', [
                                'model' => $paymentReceived,
                            ])
                            ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                            ->setTo($paymentReceived->donatedBy->email)
                            ->setSubject("Confirmation of your BCF contribution (Invoice#" . $paymentReceived->received_invoice_number . ")")
                            ->send();
                }
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
        Yii::$app->mailer->compose('@app/mail/invoice-mail', [
                    'model' => $model,
                ])
                ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                ->setTo($model->receiver->email)
                ->setSubject("Your Sadakah for " . $model->instalment_month . " " . $model->instalment_year . '(Invoice#' . $model->monthly_invoice_number . ')')
                ->send();
        Yii::$app->session->setFlash('success', 'Invoice successfully sent');
        return $this->redirect(['index']);
    }

    public function actionGenerate() {
        $users = \app\models\Users::find()
                ->where(['is_deleted' => 0, 'is_active' => 1])
                ->all();
        $proccessed = 0;
        if (!empty($users)) {
            foreach ($users as $user) {
                $model = MonthlyInvoice::find()
                        ->where(['receiver_id' => $user->user_id, 'instalment_month' => date('F'), 'instalment_year' => date('Y')])
                        ->one();
                if (empty($model)) {
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
                        Yii::$app->mailer->compose('@app/mail/invoice-mail', [
                                    'model' => $model,
                                ])
                                ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                                ->setTo($model->receiver->email)
                                ->setSubject("Your Sadakah for " . $model->instalment_month . " " . $model->instalment_year . '(Invoice#' . $model->monthly_invoice_number . ')')
                                ->send();
                    } else {
                        die(json_encode($model->errors));
                    }
                }
            }
        }
        if ($proccessed == 1) {
            Yii::$app->session->setFlash('success', 'Invoice successfully generated');
        } else {
            Yii::$app->session->setFlash('warning', 'No invoice generated');
        }
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
