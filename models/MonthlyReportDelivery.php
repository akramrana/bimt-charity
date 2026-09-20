<?php

namespace app\models;

use Yii;

class MonthlyReportDelivery extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'monthly_report_delivery';
    }

    public function rules()
    {
        return [
            [['user_id', 'report_month'], 'required'],
            [['user_id'], 'integer'],
            [['sent_at'], 'safe'],
            [['report_month'], 'string', 'max' => 7],
            [['user_id', 'report_month'], 'unique',
                'targetAttribute' => ['user_id', 'report_month']
            ],
        ];
    }
}