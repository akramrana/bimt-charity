<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace app\controllers;

use Yii;
use yii\rest\Controller;

/**
 * Description of WsController
 *
 * @author akram
 */
class WsController extends Controller {

    //put your code here
    public $data;
    public $message = "";
    public $customKeys = [];
    public $response_code = 200;

    public function init() {
        $headers = Yii::$app->response->headers;
        $headers->add("Cache-Control", "no-cache, no-store, must-revalidate");
        $headers->add("Pragma", "no-cache");
        $headers->add("Expires", 0);
    }

    public function behaviors() {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    // restrict access to
                    'Origin' => (YII_ENV_PROD) ? [
                'bimtcharity.org',
                    ] : ((YII_ENV_TEST) ? ['*'] : ['*']),
                    // Allow only POST and PUT methods
                    'Access-Control-Request-Method' => ['GET', 'HEAD', 'POST', 'PUT'],
                    // Allow only headers 'X-Wsse'
                    'Access-Control-Request-Headers' => ['X-Wsse', 'Content-Type'],
                    // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
                    'Access-Control-Allow-Credentials' => true,
                    // Allow OPTIONS caching
                    'Access-Control-Max-Age' => 3600,
                    // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                    'Access-Control-Expose-Headers' => ['X-Pagination-Current-Page'],
                ],
            ],
        ];
    }

    /**
     * 
     * @param type $action
     * @return mixed
     */
    public function beforeAction($action) {
        if (
                $action->id == "register" || 'forgot-password'
        ) {
            Yii::$app->controller->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * 
     * @return array
     */
    private function response() {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $response = $this->data;
        if (empty($response)) {
            // $response = new \stdClass();
            $response = [];
        }

        $data = [
            'success' => Yii::$app->response->isSuccessful,
            'status' => $this->response_code,
            'message' => $this->message,
            'data' => $response,
        ];

        if (!empty($this->customKeys)) {
            $data = array_merge($data, $this->customKeys);
        }

        return $data;
    }

    public function actionLogin() {
        if (Yii::$app->request->isPost) {
            $request = Yii::$app->request->bodyParams;
            if (!empty($request)) {
                $model = \app\models\Users::find()
                        ->where(['email' => $request['email'], 'is_deleted' => 0, 'is_active' => 1, 'is_approved' => 1])
                        ->one();
                if (!empty($model)) {
                    $validate = Yii::$app->security->validatePassword($request['password'], $model->password);
                    if ($validate) {
                        $this->data = [
                            'id' => (string) $model->user_id,
                            'fullname' => $model->fullname,
                            'member_code' => $model->member_code,
                            'email' => $model->email,
                            'image' => (string) ($model->image != "") ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->image) : 'https://myspace.com/common/images/user.png',
                            'phone' => $model->phone,
                            'user_type' => $model->user_type,
                            'recurring_amount' => $model->recurring_amount,
                            'created_at' => $model->created_at,
                            'batch' => $model->batch,
                            'department' => $model->department,
                        ];
                    } else {
                        $this->response_code = 201;
                        $this->message = 'Invalid password';
                        $this->data = new \stdClass();
                    }
                } else {
                    $this->response_code = 404;
                    $this->message = 'User does not exist';
                    $this->data = new \stdClass();
                }
            } else {
                $this->response_code = 500;
                $this->message = 'There was an error processing the request. Please try again later.';
                $this->data = new \stdClass();
            }

            return $this->response();
        }
    }

    public function actionDashboard($user_id) {
        $userModel = \app\models\Users::find()
                ->where(['user_id' => $user_id, 'is_deleted' => 0, 'is_active' => 1, 'is_approved' => 1])
                ->one();

        if (empty($userModel)) {
            $this->response_code = 403;
            $this->message = 'User not found.';
            return $this->response();
        }

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

        $payment_received = \app\models\PaymentReceived::find()
                ->select(['COUNT(payment_received_id) as receive_count'])
                ->where(['is_deleted' => 0])
                ->andWhere(['BETWEEN', 'DATE(created_at)', $monthStart, $monthEnd])
                ->asArray()
                ->one();

        $payment_release = \app\models\PaymentRelease::find()
                ->select(['COUNT(payment_release_id) as release_count'])
                ->where(['is_deleted' => 0])
                ->andWhere(['BETWEEN', 'DATE(created_at)', $monthStart, $monthEnd])
                ->asArray()
                ->one();

        $expenses = \app\models\Expenses::find()
                ->select(['COUNT(expense_id) as expense_count'])
                ->where(['is_deleted' => 0])
                ->andWhere(['BETWEEN', 'DATE(created_at)', $monthStart, $monthEnd])
                ->asArray()
                ->one();

        $fund_request = \app\models\FundRequests::find()
                ->select(['COUNT(fund_request_id) as fund_request_count'])
                ->where(['is_deleted' => 0])
                ->andWhere(['BETWEEN', 'DATE(created_at)', $monthStart, $monthEnd])
                ->asArray()
                ->one();

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

        $this->data = [
            'users' => $users,
            'monthly_invoice' => $monthlyInvoice['invoice_count'],
            'payment_received' => $payment_received['receive_count'],
            'payment_release' => $payment_release['release_count'],
            'expenses' => $expenses['expense_count'],
            'fund_request' => $fund_request['fund_request_count'],
            'stats' => $stats,
        ];
        return $this->response();
    }

    public function actionMonthlyInvoice($user_id) {
        $userModel = \app\models\Users::find()
                ->where(['user_id' => $user_id, 'is_deleted' => 0, 'is_active' => 1, 'is_approved' => 1])
                ->one();

        if (empty($userModel)) {
            $this->response_code = 403;
            $this->message = 'User not found.';
            return $this->response();
        }

        $searchModel = new \app\models\MonthlyInvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->prepare();

        $pagination = $dataProvider->getPagination();

        $meta = [
            'page' => $pagination->getPage() + 1, // Yii page index is 0-based
            'pageCount' => $pagination->getPageCount(),
            'totalCount' => $dataProvider->getTotalCount(),
            'pageSize' => $pagination->getPageSize(),
        ];

        $this->data = [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination' => $meta,
        ];

        return $this->response();
    }

    public function actionPaymentReceived($user_id) {
        $userModel = \app\models\Users::find()
                ->where(['user_id' => $user_id, 'is_deleted' => 0, 'is_active' => 1, 'is_approved' => 1])
                ->one();

        if (empty($userModel)) {
            $this->response_code = 403;
            $this->message = 'User not found.';
            return $this->response();
        }

        $searchModel = new \app\models\PaymentReceivedSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->prepare();

        $pagination = $dataProvider->getPagination();

        $meta = [
            'page' => $pagination->getPage() + 1, // Yii page index is 0-based
            'pageCount' => $pagination->getPageCount(),
            'totalCount' => $dataProvider->getTotalCount(),
            'pageSize' => $pagination->getPageSize(),
        ];

        $this->data = [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination' => $meta,
        ];

        return $this->response();
    }

    public function actionFundRequest($user_id) {
        $userModel = \app\models\Users::find()
                ->where(['user_id' => $user_id, 'is_deleted' => 0, 'is_active' => 1, 'is_approved' => 1])
                ->one();

        if (empty($userModel)) {
            $this->response_code = 403;
            $this->message = 'User not found.';
            return $this->response();
        }

        $searchModel = new \app\models\FundRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->prepare();

        $pagination = $dataProvider->getPagination();

        $meta = [
            'page' => $pagination->getPage() + 1, // Yii page index is 0-based
            'pageCount' => $pagination->getPageCount(),
            'totalCount' => $dataProvider->getTotalCount(),
            'pageSize' => $pagination->getPageSize(),
        ];

        $this->data = [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination' => $meta,
        ];

        return $this->response();
    }
    
    public function actionPaymentRelease($user_id) {
        $userModel = \app\models\Users::find()
                ->where(['user_id' => $user_id, 'is_deleted' => 0, 'is_active' => 1, 'is_approved' => 1])
                ->one();

        if (empty($userModel)) {
            $this->response_code = 403;
            $this->message = 'User not found.';
            return $this->response();
        }
        
        $searchModel = new \app\models\PaymentReleaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $dataProvider->prepare();

        $pagination = $dataProvider->getPagination();

        $meta = [
            'page' => $pagination->getPage() + 1, // Yii page index is 0-based
            'pageCount' => $pagination->getPageCount(),
            'totalCount' => $dataProvider->getTotalCount(),
            'pageSize' => $pagination->getPageSize(),
        ];

        $this->data = [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination' => $meta,
        ];

        return $this->response();
    }
}
