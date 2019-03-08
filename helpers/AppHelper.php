<?php

namespace app\helpers;

use Yii;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AppHelper
 *
 * @author akram
 */
class AppHelper {

    //put your code here
    static function getUserTypeName($type) {
        $types = ['S' => 'Super Admin', 'A' => 'Admin', 'M' => 'Moderator', 'G' => 'General'];
        return $types[$type];
    }

    static function addActivity($type, $type_id, $msg) {
        $model = new \app\models\Notifications();
        $model->type = $type;
        $model->type_id = $type_id;
        $model->comments = $msg;
        $model->added_by = Yii::$app->user->identity->user_id;
        $model->created_at = date("Y-m-d H:i:s");
        $model->save();
    }

    static function getAllUsers() {
        $models = \app\models\User::find()->where(['is_deleted' => 0])->all();
        $list = \yii\helpers\ArrayHelper::map($models, 'user_id', 'fullname');
        return $list;
    }

    static function getNextMonthlyInvoiceNumber() {
        $order = \app\models\MonthlyInvoice::find()
                ->select(['MAX(`monthly_invoice_number`) AS monthly_invoice_number'])
                ->asArray()
                ->one();

        if (!empty($order['monthly_invoice_number'])) {
            return $order['monthly_invoice_number'] + 1;
        } else {
            return 10000001;
        }
    }
    
    static function getReceivePayInvoiceNumber() {
        $order = \app\models\PaymentReceived::find()
                ->select(['MAX(SUBSTRING(`received_invoice_number`,4)) AS received_invoice_number'])
                ->asArray()
                ->one();

        if (!empty($order['received_invoice_number'])) {
            return 'RI-'.($order['received_invoice_number'] + 1);
        } else {
            return "RI-100001";
        }
    }
    
    static function getFundRequestInvoiceNumber()
    {
        $order = \app\models\FundRequests::find()
                ->select(['MAX(SUBSTRING(`fund_request_number`,4)) AS fund_request_number'])
                ->asArray()
                ->one();

        if (!empty($order['fund_request_number'])) {
            return 'FR-'.($order['fund_request_number'] + 1);
        } else {
            return "FR-100001";
        }
    }

    static function monthList() {
        return [
            'January' => 'January',
            'February' => 'February',
            'March' => 'March',
            'April' => 'April',
            'May' => 'May',
            'June' => 'June',
            'July' => 'July',
            'August' => 'August',
            'September' => 'September',
            'October' => 'October',
            'November' => 'November',
            'December' => 'December',
        ];
    }
    
    static function YearsList() {
        $year = 2019;
        $yearArray = [];
        for($i=$year;$i<=date('Y');$i++)
        {
            $yearArray[$i] = $i;
        }
        return $yearArray;
    }

    static function getPaidInvoiceList()
    {
        $model = \app\models\MonthlyInvoice::find()
                ->where(['is_deleted' => 0,'is_paid' => 1])
                ->orderBy(['monthly_invoice_id' => SORT_DESC])
                ->all();
        $list = \yii\helpers\ArrayHelper::map($model, 'monthly_invoice_id', 'monthly_invoice_number');
        return $list;
    }
    
    static function getStatusList()
    {
        $model = \app\models\Status::find()
                ->where(['is_deleted' => 0])
                ->orderBy(['status_id' => SORT_ASC])
                ->all();
        $list = \yii\helpers\ArrayHelper::map($model, 'status_id', 'name');
        return $list;
    }
}
