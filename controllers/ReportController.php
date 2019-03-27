<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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

/**
 * Description of ReportController
 *
 * @author akram
 */
class ReportController extends Controller {

    //put your code here
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => [
                            UserIdentity::ROLE_SUPER_ADMIN,
                            UserIdentity::ROLE_ADMIN,
                            UserIdentity::ROLE_MODERATOR,
                            UserIdentity::ROLE_GENERAL_USER,
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Notifications models.
     * @return mixed
     */
    public function actionIndex($instalment_month = "", $instalment_year = "") {
        $totalMembers = \app\models\Users::find()
                ->where(['is_deleted' => 0, 'is_active' => 1])
                ->count();
        $paymentReceived = 0;
        $incoming = 0;
        $outgoing = 0;
        $users = [];
        if ($instalment_month != "" && $instalment_year != "") {
            $paymentReceived = \app\models\PaymentReceived::find()
                    ->where(['instalment_month' => $instalment_month, 'instalment_year' => $instalment_year])
                    ->andWhere(['is_deleted' => 0])
                    ->groupBy('donated_by')
                    ->count();
            $incoming = \app\models\PaymentReceived::find()
                    ->select('SUM(amount) as amount')
                    ->where(['instalment_month' => $instalment_month, 'instalment_year' => $instalment_year])
                    ->andWhere(['is_deleted' => 0])
                    ->one();

            $monthStart = date("Y-m-d", strtotime('first day of ' . $instalment_month. ' '.$instalment_year));
            $monthEnd = date("Y-m-d", strtotime("last day of " . $instalment_month. ' '.$instalment_year));
            
            $outgoing = \app\models\PaymentRelease::find()
                    ->select('SUM(amount) as amount')
                    ->where(['BETWEEN','DATE(created_at)',$monthStart,$monthEnd])
                    ->andWhere(['is_deleted' => 0])
                    ->one();
            
            $users = \app\models\Users::find()
                    ->where(['is_deleted' => 0,'is_active' => 1])
                    ->andWhere(['BETWEEN','DATE(created_at)',$monthStart,$monthEnd])
                    ->asArray()
                    ->all();
        }

        return $this->render('index', [
                    'totalMembers' => $totalMembers,
                    'paymentReceived' => $paymentReceived,
                    'incoming' => $incoming,
                    'outgoing' => $outgoing,
                    'users' => $users,
        ]);
    }

}
