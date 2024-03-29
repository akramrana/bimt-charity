<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\components\UserIdentity;
use app\components\AccessRule;

class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['logout', 'index', 'send-mail'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'edit-profile', 'send-mail'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_SUPER_ADMIN,
                        ]
                    ],
                    [
                        'actions' => ['index', 'edit-profile'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_ADMIN,
                            UserIdentity::ROLE_MODERATOR,
                            UserIdentity::ROLE_GENERAL_USER,
                        ]
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {
        $monthStart = date("Y-m-d", strtotime('first day of this month'));
        $monthEnd = date("Y-m-d", strtotime("last day of this month"));

        $users = \app\models\Users::find()
                ->where(['is_deleted' => 0])
                ->count();
        $monthlyInvoice = \app\models\MonthlyInvoice::find()
                ->select(['COUNT(monthly_invoice_id) as invoice_count'])
                ->where(['is_deleted' => 0])
                ->andWhere(['BETWEEN', 'DATE(created_at)', $monthStart, $monthEnd])
                ->asArray()
                ->one();
        $monthlyInvoiceCurrencyWise = \app\models\MonthlyInvoice::find()
                ->select(['SUM(amount) as amount', 'currencies.code'])
                ->join('LEFT JOIN', 'currencies', 'monthly_invoice.currency_id = currencies.currency_id')
                ->where(['is_deleted' => 0])
                ->andWhere(['BETWEEN', 'DATE(created_at)', $monthStart, $monthEnd])
                ->groupBy('monthly_invoice.currency_id')
                ->asArray()
                ->all();
        //
        $payment_received = \app\models\PaymentReceived::find()
                ->select(['COUNT(payment_received_id) as receive_count'])
                ->where(['is_deleted' => 0])
                ->andWhere(['BETWEEN', 'DATE(created_at)', $monthStart, $monthEnd])
                ->asArray()
                ->one();
        $paymentReceivedCurrencyWise = \app\models\PaymentReceived::find()
                ->select(['SUM(amount) as amount', 'currencies.code'])
                ->join('LEFT JOIN', 'currencies', 'payment_received.currency_id = currencies.currency_id')
                ->where(['is_deleted' => 0])
                ->andWhere(['BETWEEN', 'DATE(created_at)', $monthStart, $monthEnd])
                ->groupBy('payment_received.currency_id')
                ->asArray()
                ->all();
        //
        $payment_release = \app\models\PaymentRelease::find()
                ->select(['COUNT(payment_release_id) as release_count'])
                ->where(['is_deleted' => 0])
                ->andWhere(['BETWEEN', 'DATE(created_at)', $monthStart, $monthEnd])
                ->asArray()
                ->one();
        $paymentReleaseCurrencyWise = \app\models\PaymentRelease::find()
                ->select(['SUM(amount) as amount', 'currencies.code'])
                ->join('LEFT JOIN', 'currencies', 'payment_release.currency_id = currencies.currency_id')
                ->where(['is_deleted' => 0])
                ->andWhere(['BETWEEN', 'DATE(created_at)', $monthStart, $monthEnd])
                ->groupBy('payment_release.currency_id')
                ->asArray()
                ->all();
        //
        $expenses = \app\models\Expenses::find()
                ->select(['COUNT(expense_id) as expense_count'])
                ->where(['is_deleted' => 0])
                ->andWhere(['BETWEEN', 'DATE(created_at)', $monthStart, $monthEnd])
                ->asArray()
                ->one();
        $expensesCurrencyWise = \app\models\Expenses::find()
                ->select(['SUM(amount) as amount', 'currencies.code'])
                ->join('LEFT JOIN', 'currencies', 'expenses.currency_id = currencies.currency_id')
                ->where(['is_deleted' => 0])
                ->andWhere(['BETWEEN', 'DATE(created_at)', $monthStart, $monthEnd])
                ->groupBy('expenses.currency_id')
                ->asArray()
                ->all();
        //
        $fund_request = \app\models\FundRequests::find()
                ->select(['COUNT(fund_request_id) as fund_request_count'])
                ->where(['is_deleted' => 0])
                ->andWhere(['BETWEEN', 'DATE(created_at)', $monthStart, $monthEnd])
                ->asArray()
                ->one();

        $fundRequestCurrencyWise = \app\models\FundRequests::find()
                ->select(['SUM(request_amount) as amount', 'currencies.code'])
                ->join('LEFT JOIN', 'currencies', 'fund_requests.currency_id = currencies.currency_id')
                ->where(['is_deleted' => 0])
                ->andWhere(['BETWEEN', 'DATE(created_at)', $monthStart, $monthEnd])
                ->groupBy('fund_requests.currency_id')
                ->asArray()
                ->all();

        //
        $status = \app\models\Status::find()
                ->where(['is_deleted' => 0])
                ->all();
        $stats = [];
        foreach ($status as $sts) {
            $fund_stat_query = \app\models\FundRequests::find()
                    ->select([
                        'SUM(request_amount) as amount',
                        'COUNT(fund_requests.fund_request_id) as fund_request_count',
                        'temp.status_id',
                        'status.name',
                    ])
                    ->join('LEFT JOIN', '(
                                        SELECT t1.*
                                        FROM fund_request_status AS t1
                                        LEFT OUTER JOIN fund_request_status AS t2 ON t1.fund_request_id = t2.fund_request_id 
                                                AND (t1.created_at < t2.created_at 
                                                 OR (t1.created_at = t2.created_at AND t1.fund_request_status_id < t2.fund_request_status_id))
                                        WHERE t2.fund_request_id IS NULL
                                        ) as temp', 'temp.fund_request_id = fund_requests.fund_request_id')
                    ->join('LEFT JOIN', 'status', 'temp.status_id = status.status_id')
                    ->where(['fund_requests.is_deleted' => 0, 'temp.status_id' => $sts->status_id])
                    ->groupBy('status_id');
            $fund_stats = $fund_stat_query->asArray()->one();

            $fund_stat_curr_wise_query = \app\models\FundRequests::find()
                    ->select([
                        'SUM(request_amount) as amount',
                        'temp.status_id',
                        'status.name',
                        'currencies.code',
                    ])
                    ->join('LEFT JOIN', '(
                                        SELECT t1.*
                                        FROM fund_request_status AS t1
                                        LEFT OUTER JOIN fund_request_status AS t2 ON t1.fund_request_id = t2.fund_request_id 
                                                AND (t1.created_at < t2.created_at 
                                                 OR (t1.created_at = t2.created_at AND t1.fund_request_status_id < t2.fund_request_status_id))
                                        WHERE t2.fund_request_id IS NULL
                                        ) as temp', 'temp.fund_request_id = fund_requests.fund_request_id')
                    ->join('LEFT JOIN', 'status', 'temp.status_id = status.status_id')
                    ->join('LEFT JOIN', 'currencies', 'fund_requests.currency_id = currencies.currency_id')
                    ->where(['fund_requests.is_deleted' => 0, 'temp.status_id' => $sts->status_id])
                    ->groupBy('fund_requests.currency_id');
            $fund_stat_curr_wise = $fund_stat_curr_wise_query->asArray()->all();

            $d = [
                'name' => $sts->name,
                'amount' => !empty($fund_stats['amount']) ? $fund_stats['amount'] : "0",
                'fund_request_count' => !empty($fund_stats['fund_request_count']) ? $fund_stats['fund_request_count'] : "0",
                'fund_stat_curr_wise' => $fund_stat_curr_wise,
            ];

            array_push($stats, $d);
        }

        $login_history = \app\models\LoginHistory::find()
                ->limit(10)
                ->orderBy(['login_history_id' => SORT_DESC])
                ->all();

        $activity_log = \app\models\Notifications::find()
                ->limit(10)
                ->where(['is_deleted' => 0])
                ->orderBy(['notification_id' => SORT_DESC])
                ->all();
        return $this->render('index', [
                    'users' => $users,
                    'monthlyInvoice' => $monthlyInvoice,
                    'monthlyInvoiceCurrencyWise' => $monthlyInvoiceCurrencyWise,
                    'payment_received' => $payment_received,
                    'paymentReceivedCurrencyWise' => $paymentReceivedCurrencyWise,
                    'payment_release' => $payment_release,
                    'paymentReleaseCurrencyWise' => $paymentReleaseCurrencyWise,
                    'expenses' => $expenses,
                    'expensesCurrencyWise' => $expensesCurrencyWise,
                    'fund_request' => $fund_request,
                    'fundRequestCurrencyWise' => $fundRequestCurrencyWise,
                    'stats' => $stats,
                    'login_history' => $login_history,
                    'activity_log' => $activity_log,
        ]);
    }

    public function actionEditProfile() {
        $model = \app\models\Users::find()
                ->where(['is_deleted' => 0, 'is_active' => 1, 'user_id' => Yii::$app->user->identity->user_id])
                ->one();
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            if (!empty($request['Users']['confirm_password'])) {
                $password = $request['Users']['confirm_password'];
                $model->password = Yii::$app->security->generatePasswordHash($password);
            }
            if ($model->save()) {
                $msg = 'Profile of ' . $model->fullname . ' has been updated by ' . Yii::$app->user->identity->fullname;
                \app\helpers\AppHelper::addActivity("US", $model->user_id, $msg);
                Yii::$app->session->setFlash('success', 'Profile successfully updated');
                return $this->redirect(['edit-profile']);
            } else {
                return $this->render('edit-profile', [
                            'model' => $model
                ]);
            }
        }
        return $this->render('edit-profile', [
                    'model' => $model
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
                    'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact() {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
                    'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout() {
        return $this->render('about');
    }

    public function actionRegister() {
        return $this->redirect(['login']);
        $model = new \app\models\Users();
        $model->scenario = 'create';
        $model->created_at = date('Y-m-d H:i:s');
        $model->updated_at = date('Y-m-d H:i:s');
        $model->enable_login = 1;
        $model->recurring_amount = 500;
        $model->currency_id = 13;
        $model->user_type = 'G';
        //
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            $password = $request['Users']['password_hash'];
            $model->password = Yii::$app->security->generatePasswordHash($password);
            if ($model->save()) {
                Yii::$app->mailer->compose('@app/mail/register', [
                            'model' => $model,
                            'password' => $password,
                        ])
                        ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                        ->setTo($model->email)
                        ->setSubject("Welcome to BIMT Charity Foundation")
                        ->send();

                Yii::$app->session->setFlash('success', 'Registration successfully completed');
                //
                return $this->redirect(['register']);
            } else {
                echo json_encode($model->errors);
                return $this->render('register', [
                            'model' => $model,
                ]);
            }
        }
        return $this->render('register', [
                    'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionSendMail() {
        $model = new \app\models\SendMailForm();
        if ($model->load(Yii::$app->request->post())) {
            $request = Yii::$app->request->bodyParams;
            //debugPrint($request);
            //exit;
            $emails = [];
            $sendMailForm = $request['SendMailForm'];
            if (!empty($sendMailForm['userIds'])) {
                $users = \app\models\Users::find()
                        ->where(['user_id' => $sendMailForm['userIds']])
                        ->andWhere(['is_active' => 1, 'is_deleted' => 0])
                        ->all();
                if (!empty($users)) {
                    foreach ($users as $usr) {
                        $emails[] = $usr->email;
                    }
                }
            } else {
                $users = \app\models\Users::find()
                        ->andWhere(['is_active' => 1, 'is_deleted' => 0])
                        ->all();
                if (!empty($users)) {
                    foreach ($users as $usr) {
                        $emails[] = $usr->email;
                    }
                }
            }
            if (!empty($emails)) {
                $fileName = !empty($sendMailForm['attachment']) ? 'uploads/' . $sendMailForm['attachment'] : "";
                if (!empty($sendMailForm['attachment'])) {
                    Yii::$app->mailer->compose('@app/mail/sent-email', [
                                'message' => $sendMailForm['message'],
                            ])
                            ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                            ->setTo($emails)
                            //->setBcc($emails)
                            ->setSubject($sendMailForm['subject'])
                            ->attach($fileName)
                            ->send();
                } else {
                    Yii::$app->mailer->compose('@app/mail/sent-email', [
                                'message' => $sendMailForm['message'],
                            ])
                            ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                            ->setTo($emails)
                            //->setBcc($emails)
                            ->setSubject($sendMailForm['subject'])
                            ->send();
                }
                Yii::$app->session->setFlash('success', 'Mail successfully sent');
                //
                return $this->redirect(['send-mail']);
            }
        }
        return $this->render('send-mail', [
                    'model' => $model
        ]);
    }

}
