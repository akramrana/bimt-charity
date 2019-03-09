<?php

namespace app\controllers;

use Yii;
use app\models\PaymentRelease;
use app\models\PaymentReleaseSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
/**
 * PaymentReleaseController implements the CRUD actions for PaymentRelease model.
 */
class PaymentReleaseController extends Controller {

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
                'only' => ['index', 'view', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_SUPER_ADMIN,
                            UserIdentity::ROLE_ADMIN,
                        ]
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update'],
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
     * Lists all PaymentRelease models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new PaymentReleaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PaymentRelease model.
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
     * Creates a new PaymentRelease model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new PaymentRelease();
        $model->created_at = date('Y-m-d H:i:s');
        $model->updated_at = date('Y-m-d H:i:s');
        $model->release_invoice_number = \app\helpers\AppHelper::getReleaseInvoiceNumber();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $msg = 'Invoice#' . $model->release_invoice_number . ' generated against fund request #' . $model->fundRequest->fund_request_number . ' Created by ' . Yii::$app->user->identity->fullname;
            \app\helpers\AppHelper::addActivity("PREL", $model->payment_release_id, $msg);
            Yii::$app->session->setFlash('success', 'Payment Release invoice successfully added');
            return $this->redirect(['view', 'id' => $model->payment_release_id]);
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing PaymentRelease model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->updated_at = date('Y-m-d H:i:s');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $msg = 'Invoice#' . $model->release_invoice_number . ' Updated by ' . Yii::$app->user->identity->fullname;
            \app\helpers\AppHelper::addActivity("PREL", $model->payment_release_id, $msg);
            Yii::$app->session->setFlash('success', 'Payment Release invoice successfully updated');
            return $this->redirect(['view', 'id' => $model->payment_release_id]);
        }

        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PaymentRelease model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        $model->save();
        $msg = 'Invoice#' . $model->release_invoice_number . ' Deleted by ' . Yii::$app->user->identity->fullname;
        \app\helpers\AppHelper::addActivity("PREL", $model->payment_release_id, $msg);
        Yii::$app->session->setFlash('success', 'Payment Release invoice successfully deleted');
        return $this->redirect(['index']);
    }

    /**
     * Finds the PaymentRelease model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PaymentRelease the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = PaymentRelease::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
