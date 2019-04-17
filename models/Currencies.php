<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "currencies".
 *
 * @property int $currency_id
 * @property string $name
 * @property string $code
 *
 * @property Expenses[] $expenses
 * @property FundRequests[] $fundRequests
 * @property MonthlyInvoice[] $monthlyInvoices
 * @property PaymentReceived[] $paymentReceiveds
 * @property PaymentRelease[] $paymentReleases
 */
class Currencies extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currencies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            [['name', 'code'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'currency_id' => 'Currency ID',
            'name' => 'Name',
            'code' => 'Currency',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpenses()
    {
        return $this->hasMany(Expenses::className(), ['currency_id' => 'currency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFundRequests()
    {
        return $this->hasMany(FundRequests::className(), ['currency_id' => 'currency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMonthlyInvoices()
    {
        return $this->hasMany(MonthlyInvoice::className(), ['currency_id' => 'currency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentReceiveds()
    {
        return $this->hasMany(PaymentReceived::className(), ['currency_id' => 'currency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentReleases()
    {
        return $this->hasMany(PaymentRelease::className(), ['currency_id' => 'currency_id']);
    }
}
