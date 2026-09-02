<?php

namespace app\controllers;

use Yii;
use app\models\Users;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;

/**
 * UserController implements the CRUD actions for Users model.
 */
class UserController extends Controller {

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
                'only' => ['index', 'view', 'create', 'update', 'delete', 'activate', 'resend'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'activate', 'resend'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_SUPER_ADMIN,
                            UserIdentity::ROLE_ADMIN,
                        ]
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'activate'],
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
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Users model.
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
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Users();
        $model->scenario = 'create';
        $model->created_at = date('Y-m-d H:i:s');
        $model->updated_at = date('Y-m-d H:i:s');
        $model->enable_login = 1;
        $model->recurring_amount = 500;
        $model->currency_id = 13;
        $model->member_code = \app\helpers\AppHelper::getNextMemberCode();

        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            $password = $request['Users']['password_hash'];
            $model->password = Yii::$app->security->generatePasswordHash($password);
            if ($model->save()) {
                //
                /* Yii::$app->mailer->compose('@app/mail/register', [
                  'model' => $model,
                  'password' => $password,
                  ])
                  ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                  ->setTo($model->email)
                  ->setSubject("Welcome to BIMT Charity Foundation")
                  ->send(); */

                $subject = "Welcome to BIMT Charity Foundation";

                $mailDetails = "<p>
                                Assalamualaikum,<br/>
                                Dear Brother " . $model->fullname . ",
                            </p>
                            <p>
                                Alhamdulillah! We are very much happy to hear that you are going to be a family member of our<br/> 
                                'BIMT Charity Foundation' family. Our lots of good wishes and DUA for our brother, who has invited<br/> 
                                you to such an organization which might bring for you and for us a way to acquire the satisfaction of<br/>
                                almighty Allah in Dunya and in Akhira.
                            </p>
                            <p>
                                Just now your necessary information has been submitted in our Web portal.
                            </p>
                            <div>
                                <p>Next Step:</p>
                                <ul>
                                    <li style=\"list-style-type: decimal\">One of our admins will review your profile and then your profile shall be activated or rejected.</li>
                                    <li style=\"list-style-type: decimal\">You will receive another email, regarding activation status of your profile</li>
                                    <li style=\"list-style-type: decimal\">If your profile is activated, then you will receive all necessary information within a separate Email.</li>
                                </ul>
                            </div>
                            <div>
                                <p>
                                    Please store this credential in secure place, you can use this information to login our web portal<br/>
                                    <b>after activation of your profile</b>.
                                </p>
                                <p>
                                    User ID:" . $model->email . "<br/>
                                    Password:" . $password . "
                                </p>
                            </div>
                            <p>
                                May Allah accept all our goodness and efforts and make these a good reason to achieve his<br/>
                                satisfaction in Dunya and in Akhira.
                            </p>
                            <p>
                                ‘মুমিনগণ! তোমাদের ধন-সম্পদ ও সন্তান-সন্ততি যেন তোমাদের আল্লাহর স্মরণ থেকে গাফেল না করে। যারা এ কারণে গাফেল হয়, তারাই তো ক্ষতিগ্রস্ত। আমি তোমাদের যা দিয়েছি তা থেকে মৃত্যু আসার আগেই ব্যয় কর। অন্যথায় সে বলবে, হে আমার পালনকর্তা, আমাকে আরও কিছুকাল অবকাশ দিলেন না কেন? তাহলে আমি সদকা করতাম এবং সৎ কর্মীদের অন্তর্ভুক্ত হতাম।’  (সূরা মুনাফিকুন : ৯-১০)
                            </p>
                            <p>
                                Ma'assalam,
                                Member Coordination Board<br/>
                                BIMT Charity Foundation<br/>
                            </p>";

                $mailObject = [
                    'from' => "BIMT Charity Foundation<communication@bimtcharity.org>",
                    'to' => $model->email,
                    'subject' => $subject,
                    'html' => $mailDetails,
                ];
                \app\helpers\AppHelper::resendEmail($mailObject);

                Yii::$app->session->setFlash('success', 'User successfully added');
                //
                $msg = $model->fullname . ' has been added by ' . Yii::$app->user->identity->fullname;
                \app\helpers\AppHelper::addActivity("US", $model->user_id, $msg);

                $pushHelper = new \app\helpers\PushHelper();
                $pushHelper->sendPush([
                    'title' => 'New Member Added',
                    'body' => $model->fullname . ' has joined BIMT Charity Foundation.',
                    'screen' => 'member',
                    'id' => $model->user_id,
                ]);

                return $this->redirect(['view', 'id' => $model->user_id]);
            } else {
                echo json_encode($model->errors);
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
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            if (!empty($request['Users']['confirm_password'])) {
                $password = $request['Users']['confirm_password'];
                $model->password = Yii::$app->security->generatePasswordHash($password);
            }
            if ($model->save()) {
                $msg = 'Profile of ' . $model->fullname . ' has been updated by ' . Yii::$app->user->identity->fullname;
                \app\helpers\AppHelper::addActivity("US", $model->user_id, $msg);
                Yii::$app->session->setFlash('success', 'User successfully updated');
                return $this->redirect(['view', 'id' => $model->user_id]);
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
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        $model->save();
        $msg = $model->fullname . ' has been deleted by ' . Yii::$app->user->identity->fullname;
        \app\helpers\AppHelper::addActivity("US", $model->user_id, $msg);
        Yii::$app->session->setFlash('success', 'User successfully deleted');
        return $this->redirect(['index']);
    }

    public function actionActivate($id) {
        $model = $this->findModel($id);

        if ($model->is_active == 0) {
            $model->is_active = 1;
            $approvalText = 'activated';
        } else {
            $model->is_active = 0;
            $approvalText = 'deactivated';
        }

        if ($model->validate() && $model->save()) {
            if ($model->is_active == 1) {
                /* Yii::$app->mailer->compose('@app/mail/activated', [
                  'model' => $model,
                  ])
                  ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                  ->setTo($model->email)
                  ->setSubject("BIMT Charity Foundation member account activated")
                  ->send(); */
                $subject = "BIMT Charity Foundation member account activated";

                $mailDetails = "<p>
                                    Assalamualaikum,<br/>
                                    Dear Brother " . $model->fullname . ",
                                </p>
                                <p>
                                    Alhamdulillah! Your profile has been reviewed and activated.<br/>
                                    We are very much happy to announce that you are now a family member of our foundation.<br/>
                                    Now you can log in to our Web portal. <br/>
                                    Web portal Link: <a href=\"http://bimtcharity.org/site/login\">http://www.bimtcharity.org/site/login</a>
                                </p>
                                <p>
                                    Username: " . $model->email . "
                                </p>
                                <p>
                                    Messenger Group Link:  <a href=\"https://m.me/join/Abb39qbdwnJ7yvdg\">https://m.me/join/Abb39qbdwnJ7yvdg</a><br/>
                                    Proposed Sadakah Amount: " . $model->recurring_amount . " " . $model->currency->code . " <br/>
                                    We pray and hope for your regular contribution even if the amount is equal to 1 BDT.<br/>
                                </p>
                                <p>
                                    InshaAllah from now on, we will work together for the betterment of our society, <br/>
                                    to help needy people and to do all kind of possible good works.
                                </p>
                                <p>
                                    ‘খেজুরের একটি টুকরা দান করে হলেও তোমরা জাহান্নামের আগুন থেকে বাঁচার চেষ্টা কর।’ (বোখারি ও মুসলিম)।
                                </p>
                                <p>
                                    Ma'assalam,<br/>
                                    Member Coordination Board<br/>
                                    BIMT Charity Foundation<br/>
                                    For details you can contact with Mohammad Abdul Motin, Kazi Symon Arif, Mustafizur Rahman, Ibrahim Ali.<br/>
                                    Web portal link: <a href=\"http://bimtcharity.org/site/login\">http://bimtcharity.org/site/login</a>
                                </p>";

                $mailObject = [
                    'from' => "BIMT Charity Foundation<communication@bimtcharity.org>",
                    'to' => $model->email,
                    'subject' => $subject,
                    'html' => $mailDetails,
                ];
                \app\helpers\AppHelper::resendEmail($mailObject);

                $pushHelper = new \app\helpers\PushHelper();
                $pushHelper->sendPushToUser($model->user_id, [
                    'title' => 'Member account activated',
                    'body' => $subject,
                    'screen' => 'member',
                    'id' => $model->user_id,
                ]);
            }
            $msg = 'Profile of ' . $model->fullname . ' ' . $approvalText . ' by ' . Yii::$app->user->identity->fullname;
            \app\helpers\AppHelper::addActivity("US", $model->user_id, $msg);
            return '1';
        } else {

            return json_encode($model->errors);
        }
    }

    public function actionResend($id) {
        $model = $this->findModel($id);
        $password = Yii::$app->security->generateRandomString(6);
        $model->password = Yii::$app->security->generatePasswordHash($password);
        if ($model->save()) {
            /* Yii::$app->mailer->compose('@app/mail/register', [
              'model' => $model,
              'password' => $password,
              ])
              ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
              ->setTo($model->email)
              ->setSubject("Welcome to BIMT Charity Foundation")
              ->send(); */

            $subject = "Welcome to BIMT Charity Foundation";

            $mailDetails = "<p>
                                Assalamualaikum,<br/>
                                Dear Brother " . $model->fullname . ",
                            </p>
                            <p>
                                Alhamdulillah! We are very much happy to hear that you are going to be a family member of our<br/> 
                                'BIMT Charity Foundation' family. Our lots of good wishes and DUA for our brother, who has invited<br/> 
                                you to such an organization which might bring for you and for us a way to acquire the satisfaction of<br/>
                                almighty Allah in Dunya and in Akhira.
                            </p>
                            <p>
                                Just now your necessary information has been submitted in our Web portal.
                            </p>
                            <div>
                                <p>Next Step:</p>
                                <ul>
                                    <li style=\"list-style-type: decimal\">One of our admins will review your profile and then your profile shall be activated or rejected.</li>
                                    <li style=\"list-style-type: decimal\">You will receive another email, regarding activation status of your profile</li>
                                    <li style=\"list-style-type: decimal\">If your profile is activated, then you will receive all necessary information within a separate Email.</li>
                                </ul>
                            </div>
                            <div>
                                <p>
                                    Please store this credential in secure place, you can use this information to login our web portal<br/>
                                    <b>after activation of your profile</b>.
                                </p>
                                <p>
                                    User ID:" . $model->email . "<br/>
                                    Password:" . $password . "
                                </p>
                            </div>
                            <p>
                                May Allah accept all our goodness and efforts and make these a good reason to achieve his<br/>
                                satisfaction in Dunya and in Akhira.
                            </p>
                            <p>
                                ‘মুমিনগণ! তোমাদের ধন-সম্পদ ও সন্তান-সন্ততি যেন তোমাদের আল্লাহর স্মরণ থেকে গাফেল না করে। যারা এ কারণে গাফেল হয়, তারাই তো ক্ষতিগ্রস্ত। আমি তোমাদের যা দিয়েছি তা থেকে মৃত্যু আসার আগেই ব্যয় কর। অন্যথায় সে বলবে, হে আমার পালনকর্তা, আমাকে আরও কিছুকাল অবকাশ দিলেন না কেন? তাহলে আমি সদকা করতাম এবং সৎ কর্মীদের অন্তর্ভুক্ত হতাম।’  (সূরা মুনাফিকুন : ৯-১০)
                            </p>
                            <p>
                                Ma'assalam,
                                Member Coordination Board<br/>
                                BIMT Charity Foundation<br/>
                            </p>";

            $mailObject = [
                'from' => "BIMT Charity Foundation<communication@bimtcharity.org>",
                'to' => $model->email,
                'subject' => $subject,
                'html' => $mailDetails,
            ];
            \app\helpers\AppHelper::resendEmail($mailObject);

            $pushHelper = new \app\helpers\PushHelper();
            $pushHelper->sendPushToUser($model->user_id, [
                'title' => 'Assalamualaikum!',
                'body' => $subject,
                'screen' => 'member',
                'id' => $model->user_id,
            ]);

            Yii::$app->session->setFlash('success', 'User info successfully sent');
            return $this->redirect(['index']);
        }
    }

    public function actionSendPush() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $userId = Yii::$app->request->post('user_id');
        $message = trim(Yii::$app->request->post('message', ''));
        if (empty($userId) || empty($message)) {
            return [
                'success' => false,
                'message' => 'User and message are required.'
            ];
        }
        $user = \app\models\Users::findOne($userId);
        if (empty($user)) {
            return [
                'success' => false,
                'message' => 'User not found.'
            ];
        }
        $pushHelper = new \app\helpers\PushHelper();
        $pushHelper->sendPushToUser($user->user_id, [
            'title' => 'Assalamualaikum!',
            'body' => $message,
            'screen' => 'member',
            'id' => $user->user_id,
        ]);
        return [
            'success' => true,
            'message' => 'Push notification sent successfully.'
        ];
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
