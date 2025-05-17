<?php

namespace app\controllers;

use Yii;
use app\models\FundRequests;
use app\models\FundRequestSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
use yii\web\ForbiddenHttpException;

/**
 * FundRequestController implements the CRUD actions for FundRequests model.
 */
class FundRequestController extends Controller
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
                'only' => ['index', 'view', 'create', 'update', 'delete', 'activate', 'add-status'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'activate', 'add-status'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_SUPER_ADMIN,
                            UserIdentity::ROLE_ADMIN,
                        ]
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'activate'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_MODERATOR,
                        ]
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'withdraw'],
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
     * Lists all FundRequests models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new FundRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single FundRequests model.
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
     * Creates a new FundRequests model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new FundRequests();
        $model->scenario = 'create';
        $model->currency_id = 13;
        $model->created_at = date('Y-m-d H:i:s');
        $model->updated_at = date('Y-m-d H:i:s');
        $model->fund_request_number = \app\helpers\AppHelper::getFundRequestInvoiceNumber();
        if (\Yii::$app->session['__bimtCharityUserRole'] == 4) {
            $model->request_user_id = Yii::$app->user->identity->user_id;
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $frs = new \app\models\FundRequestStatus();
            $frs->fund_request_id = $model->fund_request_id;
            $frs->status_id = 1;
            $frs->user_id = Yii::$app->user->identity->user_id;
            $frs->comments = "";
            $frs->created_at = date('Y-m-d H:i:s');
            $frs->save();
            //
            $msg = 'Invoice#' . $model->fund_request_number . ' generated for new fund request submitted by ' . $model->requestUser->fullname . '. Created by ' . Yii::$app->user->identity->fullname;
            \app\helpers\AppHelper::addActivity("FR", $model->fund_request_id, $msg);
            //
            Yii::$app->session->setFlash('success', 'Fund request successfully added');
            return $this->redirect(['view', 'id' => $model->fund_request_id]);
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing FundRequests model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $fundStatus = \app\models\FundRequestStatus::find()
                ->where(['fund_request_id' => $model->fund_request_id])
                ->orderBy(['fund_request_status_id' => SORT_DESC])
                ->one();
        if (\Yii::$app->session['__bimtCharityUserRole'] == 4 && (Yii::$app->user->identity->user_id != $model->request_user_id)) {
            throw new ForbiddenHttpException('You are not authorized to view this page.');
        }
        if (\Yii::$app->session['__bimtCharityUserRole'] == 4 && (Yii::$app->user->identity->user_id == $model->request_user_id)) {
            if ($fundStatus->status_id != 1 && $fundStatus->status_id != 4) {
                throw new ForbiddenHttpException('You are not authorized to view this page.');
            }
        }
        $model->scenario = 'update';
        $model->updated_at = date('Y-m-d H:i:s');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $msg = 'Invoice#' . $model->fund_request_number . ' Updated by ' . Yii::$app->user->identity->fullname;
            \app\helpers\AppHelper::addActivity("FR", $model->fund_request_id, $msg);
            //
            Yii::$app->session->setFlash('success', 'Fund request successfully updated');
            return $this->redirect(['view', 'id' => $model->fund_request_id]);
        }

        return $this->render('update', [
                    'model' => $model,
        ]);
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
            $msg = 'Invoice#' . $model->fund_request_number . ' ' . $approvalText . ' by ' . Yii::$app->user->identity->fullname;
            \app\helpers\AppHelper::addActivity("FR", $model->fund_request_id, $msg);
            return '1';
        } else {

            return json_encode($model->errors);
        }
    }

    /**
     * Deletes an existing FundRequests model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        $model->save();
        //
        $msg = 'Invoice#' . $model->fund_request_number . ' Deleted by ' . Yii::$app->user->identity->fullname;
        \app\helpers\AppHelper::addActivity("FR", $model->fund_request_id, $msg);
        //
        Yii::$app->session->setFlash('success', 'Fund request successfully deleted');
        return $this->redirect(['index']);
    }

    public function actionAddStatus() {
        $request = Yii::$app->request->bodyParams;
        if (!empty($request['fund_request_id'])) {
            $model = FundRequests::find()
                    ->where(['fund_request_id' => $request['fund_request_id']])
                    ->one();
            if (!empty($model)) {
                $check = \app\models\FundRequestStatus::find()
                        ->where(['status_id' => $request['status'], 'fund_request_id' => $model->fund_request_id])
                        ->one();
                if (!empty($check)) {
                    return json_encode(['status' => 201, 'msg' => 'The fund request is already in "' . strtoupper($check->status->name) . '" status']);
                }
                if ($request['status'] == 6) {
                    return json_encode(['status' => 201, 'msg' => 'Request submitter only can withdraw']);
                }
                $underInvestigationStatus = \app\models\FundRequestStatus::find()
                        ->where(['fund_request_id' => $model->fund_request_id, 'status_id' => 5])
                        ->one();
                if (($request['status'] == 2 || $request['status'] == 3 || $request['status'] == 6) && empty($underInvestigationStatus)) {
                    return json_encode(['status' => 201, 'msg' => 'Can\'t change status without investigation']);
                }
                $status = new \app\models\FundRequestStatus();
                $status->fund_request_id = $request['fund_request_id'];
                $status->status_id = $request['status'];
                $status->user_id = Yii::$app->user->identity->user_id;
                $status->comments = !empty($request['comment']) ? $request['comment'] : "";
                $status->created_at = date('Y-m-d H:i:s');
                if ($status->save()) {
                    $msg = 'Invoice#' . $model->fund_request_number . ' has been ' . $status->status->name . ' by ' . Yii::$app->user->identity->fullname;
                    \app\helpers\AppHelper::addActivity("FR", $model->fund_request_id, $msg);
                    //
                    if ($status->status_id == 2) {
                        $prModel = new \app\models\PaymentRelease();
                        $prModel->currency_id = 13;
                        $prModel->created_at = date('Y-m-d H:i:s');
                        $prModel->updated_at = date('Y-m-d H:i:s');
                        $prModel->fund_request_id = $model->fund_request_id;
                        $prModel->release_by = Yii::$app->user->identity->user_id;
                        $prModel->amount = $model->request_amount;
                        $prModel->note = "System Added";
                        $prModel->release_invoice_number = \app\helpers\AppHelper::getReleaseInvoiceNumber();
                        $prModel->save();
                    }
                    return json_encode(['status' => 200, 'msg' => 'Fund status successfully updated.']);
                } else {
                    return json_encode($status->errors);
                }
            }
        } else {
            return json_encode(['status' => 201, 'msg' => 'Error processing your request.']);
        }
    }

    public function actionWithdraw($id) {
        $model = $this->findModel($id);
        $fundStatus = \app\models\FundRequestStatus::find()
                ->where(['fund_request_id' => $model->fund_request_id])
                ->orderBy(['fund_request_status_id' => SORT_DESC])
                ->one();
        if (\Yii::$app->session['__bimtCharityUserRole'] == 4 && (Yii::$app->user->identity->user_id == $model->request_user_id) && ($fundStatus->status_id == 1 || $fundStatus->status_id == 4)) {
            $frs = new \app\models\FundRequestStatus();
            $frs->fund_request_id = $model->fund_request_id;
            $frs->status_id = 6;
            $frs->user_id = Yii::$app->user->identity->user_id;
            $frs->comments = "";
            $frs->created_at = date('Y-m-d H:i:s');
            $frs->save();
            //
            $msg = 'Invoice#' . $model->fund_request_number . ' Withdraw by ' . Yii::$app->user->identity->fullname;
            \app\helpers\AppHelper::addActivity("FR", $model->fund_request_id, $msg);
            Yii::$app->session->setFlash('success', 'Fund request successfully Withdrawn');
            return $this->redirect(['view', 'id' => $model->fund_request_id]);
        } else {
            throw new ForbiddenHttpException('You are not authorized to view this page.');
        }
    }

    /**
     * Finds the FundRequests model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FundRequests the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = FundRequests::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
