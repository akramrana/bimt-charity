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

    static function addActivity($type, $type_id, $msg, $user_id = "") {
        $model = new \app\models\Notifications();
        $model->type = $type;
        $model->type_id = $type_id;
        $model->comments = $msg;
        if (!empty($user_id)) {
            $model->added_by = $user_id;
        } elseif (!empty(Yii::$app->user->identity)) {
            $model->added_by = Yii::$app->user->identity->user_id;
        } else {
            $model->added_by = null;
        }
        $model->created_at = date("Y-m-d H:i:s");
        $model->save();
    }

    static function addActivityCron($type, $type_id, $msg) {
        $model = new \app\models\Notifications();
        $model->type = $type;
        $model->type_id = $type_id;
        $model->comments = $msg;
        $model->added_by = 3;
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
            return 'RI-' . ($order['received_invoice_number'] + 1);
        } else {
            return "RI-100001";
        }
    }

    static function getFundRequestInvoiceNumber() {
        $order = \app\models\FundRequests::find()
                ->select(['MAX(SUBSTRING(`fund_request_number`,4)) AS fund_request_number'])
                ->asArray()
                ->one();

        if (!empty($order['fund_request_number'])) {
            return 'FR-' . ($order['fund_request_number'] + 1);
        } else {
            return "FR-100001";
        }
    }

    static function getReleaseInvoiceNumber() {
        $order = \app\models\PaymentRelease::find()
                ->select(['MAX(SUBSTRING(`release_invoice_number`,4)) AS release_invoice_number'])
                ->asArray()
                ->one();

        if (!empty($order['release_invoice_number'])) {
            return 'DI-' . ($order['release_invoice_number'] + 1);
        } else {
            return "DI-100001";
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
        for ($i = $year; $i <= date('Y'); $i++) {
            $yearArray[$i] = $i;
        }
        return $yearArray;
    }

    static function getPaidInvoiceList() {
        $model = \app\models\MonthlyInvoice::find()
                ->where(['is_deleted' => 0, 'is_paid' => 1])
                ->orderBy(['monthly_invoice_id' => SORT_DESC])
                ->all();
        $list = \yii\helpers\ArrayHelper::map($model, 'monthly_invoice_id', 'monthly_invoice_number');
        return $list;
    }

    static function getStatusList() {
        $model = \app\models\Status::find()
                ->where(['is_deleted' => 0])
                ->orderBy(['sort_order' => SORT_ASC])
                ->all();
        $list = \yii\helpers\ArrayHelper::map($model, 'status_id', 'name');
        return $list;
    }

    static function getApprovedFundRequest() {
        $query = \app\models\FundRequests::find();
        $query->join('LEFT JOIN', '(
                                        SELECT t1.*
                                        FROM fund_request_status AS t1
                                        LEFT OUTER JOIN fund_request_status AS t2 ON t1.fund_request_id = t2.fund_request_id 
                                                AND (t1.created_at < t2.created_at 
                                                 OR (t1.created_at = t2.created_at AND t1.fund_request_status_id < t2.fund_request_status_id))
                                        WHERE t2.fund_request_id IS NULL
                                        ) as temp', 'temp.fund_request_id = fund_requests.fund_request_id');
        $query->andWhere(['temp.status_id' => 2, 'is_active' => 1, 'is_deleted' => 0]);
        $model = $query->all();
        $list = \yii\helpers\ArrayHelper::map($model, 'fund_request_id', 'fund_request_number');
        return $list;
    }

    static function getUserTypeList() {
        $type = [
            'S' => 'Super Admin',
            'A' => 'Admin',
            'M' => 'Moderator',
            'G' => 'General',
        ];
        if (\Yii::$app->session['__bimtCharityUserRole'] == 2) {
            unset($type['S']);
        } else if (\Yii::$app->session['__bimtCharityUserRole'] == 3) {
            unset($type['S']);
            unset($type['A']);
        }
        return $type;
    }

    static function getAllCurrency() {
        $model = \app\models\Currencies::find()->all();
        $list = \yii\helpers\ArrayHelper::map($model, 'currency_id', 'code');
        return $list;
    }

    static function getUserPaidInvoiceList($user_id) {
        $model = \app\models\MonthlyInvoice::find()
                ->where(['is_deleted' => 0, 'is_paid' => 1])
                ->andWhere(['receiver_id' => $user_id])
                ->orderBy(['monthly_invoice_id' => SORT_DESC])
                ->all();
        $list = \yii\helpers\ArrayHelper::map($model, 'monthly_invoice_id', 'monthly_invoice_number');
        return $list;
    }

    static function getUserUnpaidInvoiceList($user_id) {
        $model = \app\models\MonthlyInvoice::find()
                ->where(['is_deleted' => 0, 'is_paid' => 0])
                ->andWhere(['receiver_id' => $user_id])
                ->orderBy(['monthly_invoice_id' => SORT_DESC])
                ->all();
        $list = \yii\helpers\ArrayHelper::map($model, 'monthly_invoice_id', 'monthly_invoice_number');
        return $list;
    }

    static function getNextMemberCode() {
        $order = \app\models\Users::find()
                ->select(['MAX(SUBSTRING(`member_code`,2)) AS member_code'])
                ->asArray()
                ->one();

        if (!empty($order['member_code'])) {
            return 'M' . ($order['member_code'] + 1);
        } else {
            return "M100001";
        }
    }

    static function getAllUsersWithEmail() {
        $models = \app\models\User::find()->where(['is_deleted' => 0])->all();
        $list = \yii\helpers\ArrayHelper::map($models, 'user_id', function ($model) {
            return $model->fullname . ': ' . $model->email . ' :' . $model->phone;
        });
        return $list;
    }

    static function hasPaidInvoiceWithinLastTwoMonths($receiver_id) {
        $query = \app\models\MonthlyInvoice::find()
                ->where([
                    'receiver_id' => $receiver_id,
                    'is_paid' => 1,
                    'is_deleted' => 0,
                ])
                ->andWhere([
                    '>=',
                    'updated_at',
                    date('Y-m-d H:i:s', strtotime('-2 months'))
        ]);
        //debugPrint($query->createCommand()->getRawSql());
        $data = $query->count();
        //debugPrint($data);
        return $data;
    }

    static function resendEmail($mailObject) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.resend.com/emails',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($mailObject),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $_ENV['RESEND_API_KEY'],
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        //debugPrint($response);
        curl_close($curl);
    }
}
