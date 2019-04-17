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
        $incoming = [];
        $outgoing = [];
        $users = [];
        $expenses = [];
        $totalBalances = [];
        if ($instalment_month != "" && $instalment_year != "") {
            $paymentReceived = \app\models\PaymentReceived::find()
                    ->where(['instalment_month' => $instalment_month, 'instalment_year' => $instalment_year])
                    ->andWhere(['is_deleted' => 0])
                    ->groupBy('donated_by')
                    ->count();
            
            $incomingQuery = \app\models\PaymentReceived::find()
                    ->select([
                        'SUM(amount) as amount',
                        'code'
                    ])
                    ->join('LEFT JOIN','currencies','payment_received.currency_id = currencies.currency_id')
                    ->where(['instalment_month' => $instalment_month, 'instalment_year' => $instalment_year])
                    ->andWhere(['is_deleted' => 0])
                    ->groupBy('payment_received.currency_id');
            $incoming = $incomingQuery->asArray()
                    ->all();

            $monthStart = date("Y-m-d", strtotime('first day of ' . $instalment_month. ' '.$instalment_year));
            $monthEnd = date("Y-m-d", strtotime("last day of " . $instalment_month. ' '.$instalment_year));
            
            $outgoing = \app\models\PaymentRelease::find()
                    ->select([
                        'SUM(amount) as amount',
                        'code'
                    ])
                    ->join('LEFT JOIN','currencies','payment_release.currency_id = currencies.currency_id')
                    ->where(['BETWEEN','DATE(payment_release.created_at)',$monthStart,$monthEnd])
                    ->andWhere(['is_deleted' => 0])
                    ->groupBy('payment_release.currency_id')
                    ->asArray()
                    ->all();
            
            $users = \app\models\Users::find()
                    ->where(['is_deleted' => 0,'is_active' => 1])
                    ->andWhere(['BETWEEN','DATE(created_at)',$monthStart,$monthEnd])
                    ->asArray()
                    ->all();
            
            $expenses = \app\models\Expenses::find()
                    ->select([
                        'SUM(amount) as amount',
                        'code'
                    ])
                    ->join('LEFT JOIN','currencies','expenses.currency_id = currencies.currency_id')
                    ->where(['is_deleted' => 0])
                    ->andWhere(['BETWEEN','DATE(expenses.created_at)',$monthStart,$monthEnd])
                    ->groupBy('expenses.currency_id')
                    ->asArray()
                    ->all();
            //debugPrint($incoming);exit;
            $totalBalanceQuery = \app\models\Currencies::find()
                    ->select([
                        'code',
                        '(
                                SELECT SUM(amount) as received_amount 
                                FROM payment_received 
                                WHERE payment_received.currency_id = currencies.currency_id AND is_deleted = 0 AND DATE(payment_received.created_at) BETWEEN "'.$monthStart.'" AND "'.$monthEnd.'"
                        ) AS received_amount',
                        '(
                                SELECT SUM(amount) as expense_amount 
                                FROM expenses 
                                WHERE expenses.currency_id = currencies.currency_id AND is_deleted = 0 AND DATE(expenses.created_at) BETWEEN "'.$monthStart.'" AND "'.$monthEnd.'"
                        ) AS expense_amount',
                        '(
                                    SELECT SUM(amount) as donate_amount 
                                    FROM payment_release 
                                    WHERE payment_release.currency_id = currencies.currency_id AND is_deleted = 0 AND DATE(payment_release.created_at) BETWEEN "'.$monthStart.'" AND "'.$monthEnd.'"
                        ) AS donate_amount'
                    ])
                    ->having('(received_amount IS NOT NULL) OR (expense_amount IS NOT NULL) OR (donate_amount IS NOT NULL)');
            
            //echo $totalBalanceQuery->createCommand()->rawSql;exit;
            
            $totalBalances = $totalBalanceQuery->asArray()->all();
            
            
        }

        
        return $this->render('index', [
                    'totalMembers' => $totalMembers,
                    'paymentReceived' => $paymentReceived,
                    'incoming' => $incoming,
                    'outgoing' => $outgoing,
                    'users' => $users,
                    'expenses' => $expenses,
                    'totalBalances' => $totalBalances,
        ]);
    }

}
