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
                        $model->logged_in_at = date("Y-m-d H:i:s");
                        $model->save(false);
                                
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
                            'address' => $model->address,
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

    public function actionUser($user_id) {
        $userModel = \app\models\Users::find()
                ->where(['user_id' => $user_id, 'is_deleted' => 0, 'is_active' => 1, 'is_approved' => 1])
                ->one();

        if (empty($userModel)) {
            $this->response_code = 403;
            $this->message = 'User not found.';
            return $this->response();
        }

        $searchModel = new \app\models\UserSearch();
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

    public function actionDocument($user_id) {
        $userModel = \app\models\Users::find()
                ->where(['user_id' => $user_id, 'is_deleted' => 0, 'is_active' => 1, 'is_approved' => 1])
                ->one();

        if (empty($userModel)) {
            $this->response_code = 403;
            $this->message = 'User not found.';
            return $this->response();
        }

        $searchModel = new \app\models\DocumentSearch();
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

    public function actionNotification($user_id) {
        $userModel = \app\models\Users::find()
                ->where(['user_id' => $user_id, 'is_deleted' => 0, 'is_active' => 1, 'is_approved' => 1])
                ->one();

        if (empty($userModel)) {
            $this->response_code = 403;
            $this->message = 'User not found.';
            return $this->response();
        }

        $searchModel = new \app\models\NotificationSearch();
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

    public function actionEditProfile() {
        $request = \Yii::$app->request->bodyParams;
        if (!empty($request)) {
            if (empty($request['user_id'])) {
                $this->response_code = 422;
                $this->message = 'User ID is required.';
                return $this->response();
            }
            $model = \app\models\Users::find()
                    ->where([
                        'user_id' => $request['user_id'],
                        'is_deleted' => 0,
                        'is_active' => 1,
                    ])
                    ->one();
            if (empty($model)) {
                $this->response_code = 403;
                $this->message = 'User not found.';
                return $this->response();
            }
            $model->fullname = $request['fullname'];
            $model->address = $request['address'];
            $model->batch = $request['batch'];
            $model->department = $request['department'];
            if (isset($request['image']) && $request['image'] !== '') {
                $image = base64_decode($request['image']);
                if ($image) {
                    $img = imagecreatefromstring($image);
                    if ($img !== false) {
                        $imageName = time() . '.png';
                        imagepng($img, Yii::$app->basePath . '/web/uploads/' . $imageName, 9);
                        imagedestroy($img);
                        $model->image = $imageName;
                    }
                }
            }
            $password = isset($request['password_hash']) ? $request['password_hash'] : (isset($request['password']) ? $request['password'] : null);
            if ($password !== null && $password !== '') {
                $confirmPassword = isset($request['confirm_password']) ? $request['confirm_password'] : null;
                if ($password !== $confirmPassword) {
                    $this->response_code = 422;
                    $this->message = 'Password and Confirm Password do not match.';
                    return $this->response();
                }
                $model->password_hash = $password;
                $model->confirm_password = $confirmPassword;
                $model->password = Yii::$app->security->generatePasswordHash($password);
            }
            $model->updated_at = date('Y-m-d H:i:s');
            if ($model->validate() && $model->save(false)) {
                $this->message = 'Profile successfully updated.';
                $this->data = [
                    'user_id' => (string) $model->user_id,
                    'fullname' => $model->fullname,
                    'address' => $model->address,
                    'batch' => $model->batch,
                    'department' => $model->department,
                    'image' => (string) ($model->image != "") ? Yii::$app->urlManager->createAbsoluteUrl('uploads/' . $model->image) : 'https://myspace.com/common/images/user.png',
                ];
            } else {
                $this->response_code = 422;
                $this->message = 'Profile could not be updated.';
                $this->data = $model->errors;
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }

    public function actionAddSadaqa() {
        $request = \Yii::$app->request->bodyParams;
        if (!empty($request)) {
            if (empty($request['user_id'])) {
                $this->response_code = 422;
                $this->message = 'User ID is required.';
                return $this->response();
            }
            $model = \app\models\Users::find()
                    ->where([
                        'user_id' => $request['user_id'],
                        'is_deleted' => 0,
                        'is_active' => 1,
                    ])
                    ->one();
            if (empty($model)) {
                $this->response_code = 403;
                $this->message = 'User not found.';
                return $this->response();
            }
            $paymentReceived = new \app\models\PaymentReceived();
            $paymentReceived->scenario = 'add-sadaqa';
            $paymentReceived->received_invoice_number = \app\helpers\AppHelper::getReceivePayInvoiceNumber();
            $paymentReceived->donated_by = $model->user_id;
            $paymentReceived->received_by = 7;
            $paymentReceived->received_date = isset($request['received_date']) ? $request['received_date'] : null;
            $paymentReceived->comments = isset($request['comments']) ? $request['comments'] : '';
            $paymentReceived->has_invoice = !empty($request['has_invoice']) ? 1 : 0;
            $paymentReceived->created_at = date('Y-m-d H:i:s');
            $paymentReceived->updated_at = date('Y-m-d H:i:s');
            if ($paymentReceived->has_invoice == 1) {
                $paymentReceived->monthly_invoice_id = isset($request['monthly_invoice_id']) ? $request['monthly_invoice_id'] : null;
                $invoice = \app\models\MonthlyInvoice::find()
                        ->where([
                            'monthly_invoice_id' => $paymentReceived->monthly_invoice_id,
                            'receiver_id' => $model->user_id,
                            'is_deleted' => 0,
                            'is_paid' => 0,
                        ])
                        ->one();
                if (empty($invoice)) {
                    $this->response_code = 422;
                    $this->message = 'Monthly invoice not found.';
                    return $this->response();
                }
                $invoice->is_paid = 1;
                $invoice->save(false);

                $paymentReceived->instalment_month = $invoice->instalment_month;
                $paymentReceived->instalment_year = $invoice->instalment_year;
                $paymentReceived->amount = $invoice->amount;
                $paymentReceived->currency_id = $invoice->currency_id;
            } else {
                $paymentReceived->monthly_invoice_id = null;
                $paymentReceived->amount = isset($request['amount']) ? $request['amount'] : null;
                $paymentReceived->currency_id = isset($request['currency_id']) ? $request['currency_id'] : 13;
                $paymentReceived->instalment_month = isset($request['instalment_month']) ? $request['instalment_month'] : null;
                $paymentReceived->instalment_year = isset($request['instalment_year']) ? $request['instalment_year'] : null;
            }
            if (isset($request['file']) && $request['file'] !== '') {
                $file = base64_decode($request['file']);
                if ($file) {
                    $img = imagecreatefromstring($file);
                    if ($img !== false) {
                        $fileName = time() . '.png';
                        imagepng($img, Yii::$app->basePath . '/web/uploads/' . $fileName, 9);
                        imagedestroy($img);
                        $paymentReceived->file = $fileName;
                    }
                }
            }
            if ($paymentReceived->save()) {
                $this->message = 'Payment Received invoice successfully added.';
                $this->data = [
                    'payment_received_id' => (string) $paymentReceived->payment_received_id,
                    'received_invoice_number' => $paymentReceived->received_invoice_number,
                    'donated_by' => (string) $paymentReceived->donated_by,
                    'received_by' => (string) $paymentReceived->received_by,
                    'received_date' => $paymentReceived->received_date,
                    'comments' => $paymentReceived->comments,
                    'has_invoice' => (int) $paymentReceived->has_invoice,
                    'monthly_invoice_id' => $paymentReceived->monthly_invoice_id,
                    'amount' => $paymentReceived->amount,
                    'currency_id' => $paymentReceived->currency_id,
                    'instalment_month' => $paymentReceived->instalment_month,
                    'instalment_year' => $paymentReceived->instalment_year,
                    'file' => $paymentReceived->file,
                ];
            } else {
                $this->response_code = 422;
                $this->message = 'Payment Received invoice could not be added.';
                $this->data = $paymentReceived->errors;
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }

    public function actionUnpaidInvoices($user_id) {
        $userModel = \app\models\Users::find()
                ->where(['user_id' => $user_id, 'is_deleted' => 0, 'is_active' => 1, 'is_approved' => 1])
                ->one();

        if (empty($userModel)) {
            $this->response_code = 403;
            $this->message = 'User not found.';
            return $this->response();
        }

        $searchModel = new \app\models\MonthlyInvoiceSearch();
        $searchModel->is_paid = 0;
        $searchModel->receiver_id = $userModel->user_id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $this->data = [
            'dataProvider' => $dataProvider,
        ];
        return $this->response();
    }

    public function actionCms($page) {
        $model = \app\models\Cms::find()
                ->where(['cms_id' => $page])
                ->one();
        if (!empty($model)) {
            $this->data = [
                'id' => $model->cms_id,
                'title' => $model->{"title"},
                'content' => $model->{"content"},
            ];
        }
        return $this->response();
    }

    public function actionDeleteAccount() {
        $request = \Yii::$app->request->bodyParams;
        if (!empty($request)) {
            if (empty($request['user_id'])) {
                $this->response_code = 422;
                $this->message = 'User ID is required.';
                return $this->response();
            }
            $model = \app\models\Users::find()
                    ->where([
                        'user_id' => $request['user_id'],
                        'is_deleted' => 0,
                        'is_active' => 1,
                    ])
                    ->one();
            if (empty($model)) {
                $this->response_code = 403;
                $this->message = 'User not found.';
                return $this->response();
            }
            $model->is_deleted = 1;
            $model->is_active = 0;
            $model->is_approved = 0;
            $model->updated_at = date('Y-m-d H:i:s');
            if ($model->validate() && $model->save(false)) {
                $this->message = 'Profile successfully deleted.';
            } else {
                $this->response_code = 422;
                $this->message = 'Profile could not be deleted.';
                $this->data = $model->errors;
            }
        } else {
            $this->response_code = 500;
            $this->message = 'There was an error processing the request. Please try again later.';
        }
        return $this->response();
    }

    public function actionSaveDeviceToken() {
        $request = \Yii::$app->request->bodyParams;
        if (empty($request)) {
            $this->response_code = 400;
            $this->message = 'Invalid request.';
            return $this->response();
        }
        $userId = $request['userId'] ?? null;
        
        $deviceId = $request['deviceId'] ?? null;
        $pushType = $request['pushType'] ?? null;
        $pushToken = $request['pushToken'] ?? null;
        // deviceId, pushType and pushToken are required
        if (empty($deviceId) || empty($pushType) || empty($pushToken)) {
            $this->response_code = 422;
            $this->message = 'Device ID, push type and push token are required.';
            return $this->response();
        }
        // Validate push type
        $allowedPushTypes = ['fcm', 'hms'];
        if (!in_array($pushType, $allowedPushTypes, true)) {
            $this->response_code = 422;
            $this->message = 'Invalid push type.';
            return $this->response();
        }
        $user = \app\models\Users::find()
                ->where([
                    'user_id' => $userId,
                    'is_deleted' => 0,
                    'is_active' => 1,
                ])
                ->one();
        if (empty($user)) {
            $this->response_code = 403;
            $this->message = 'User not found.';
            return $this->response();
        }
        try {
            $now = date('Y-m-d H:i:s');
            \Yii::$app->db->createCommand()->upsert(
                    'device_tokens',
                    [
                        'user_id' => $userId,
                        'device_id' => $deviceId,
                        'push_type' => $pushType,
                        'push_token' => $pushToken,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ],
                    [
                        'user_id' => $userId,
                        'push_token' => $pushToken,
                        'updated_at' => $now,
                    ]
            )->execute();

            $this->response_code = 200;
            $this->message = 'Token saved successfully.';
        } catch (\Exception $e) {
            \Yii::error(
                    'Save device token error: ' . $e->getMessage(),
                    __METHOD__
            );
            $this->response_code = 500;
            $this->message = 'There was an error saving the device token.';
        }
        return $this->response();
    }
}
