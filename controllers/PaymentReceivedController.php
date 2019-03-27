<?php

namespace app\controllers;

use Yii;
use app\models\PaymentReceived;
use app\models\PaymentReceivedSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\UserIdentity;
use app\components\AccessRule;
/**
 * PaymentReceivedController implements the CRUD actions for PaymentReceived model.
 */
class PaymentReceivedController extends Controller {

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
     * Lists all PaymentReceived models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new PaymentReceivedSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PaymentReceived model.
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
     * Creates a new PaymentReceived model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new PaymentReceived();
        $model->created_at = date('Y-m-d H:i:s');
        $model->updated_at = date('Y-m-d H:i:s');
        $model->received_invoice_number = \app\helpers\AppHelper::getReceivePayInvoiceNumber();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->has_invoice == 1 && !empty($model->monthly_invoice_id)) {
                $invoice = \app\models\MonthlyInvoice::findOne($model->monthly_invoice_id);
                $model->instalment_month = $invoice->instalment_month;
                $model->instalment_year = $invoice->instalment_year;
                $model->amount = $invoice->amount;
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Payment Received invoice successfully added');
                $msg = 'Invoice#' . $model->received_invoice_number . ' generated for ' . $model->instalment_month . ' ' . $model->instalment_year . ' Donated By ' . $model->donatedBy->fullname . '. Created by ' . Yii::$app->user->identity->fullname;
                \app\helpers\AppHelper::addActivity("PREC", $model->payment_received_id, $msg);
                return $this->redirect(['view', 'id' => $model->payment_received_id]);
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
     * Updates an existing PaymentReceived model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->updated_at = date('Y-m-d H:i:s');
        if ($model->load(Yii::$app->request->post())) {
            if ($model->has_invoice == 1 && !empty($model->monthly_invoice_id)) {
                $invoice = \app\models\MonthlyInvoice::findOne($model->monthly_invoice_id);
                $model->instalment_month = $invoice->instalment_month;
                $model->instalment_year = $invoice->instalment_year;
                $model->amount = $invoice->amount;
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Payment Received invoice successfully updated');
                $msg = 'Invoice#' . $model->received_invoice_number . ' updated by ' . Yii::$app->user->identity->fullname;
                \app\helpers\AppHelper::addActivity("PREC", $model->payment_received_id, $msg);
                return $this->redirect(['view', 'id' => $model->payment_received_id]);
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
     * Deletes an existing PaymentReceived model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $model->is_deleted = 1;
        $model->save();
        $msg = 'Invoice#' . $model->received_invoice_number . ' has been deleted by ' . Yii::$app->user->identity->fullname;
        \app\helpers\AppHelper::addActivity("PREC", $model->payment_received_id, $msg);
        Yii::$app->session->setFlash('success', 'Payment Received invoice successfully deleted');
        return $this->redirect(['index']);
    }
    
    public function actionSendMail($id)
    {
        $model = $this->findModel($id);
        Yii::$app->mailer->compose('@app/mail/receive-invoice-mail', [
                    'model' => $model,
                ])
                ->setFrom([Yii::$app->params['siteEmail'] => Yii::$app->params['appName']])
                ->setTo($model->donatedBy->email)
                ->setSubject("Confirmation of your BCF contribution (Invoice#".$model->received_invoice_number.")")
                ->send();
        Yii::$app->session->setFlash('success', 'Invoice successfully sent');
        return $this->redirect(['index']);
    }

    /**
     * Finds the PaymentReceived model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PaymentReceived the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = PaymentReceived::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
