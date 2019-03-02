<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "monthly_invoice".
 *
 * @property int $monthly_invoice_id
 * @property string $monthly_invoice_number
 * @property int $receiver_id
 * @property double $amount
 * @property string $instalment_month
 * @property string $instalment_year
 * @property int $is_paid
 * @property int $is_deleted
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Users $receiver
 * @property PaymentReceived[] $paymentReceiveds
 */
class MonthlyInvoice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'monthly_invoice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['monthly_invoice_number', 'receiver_id', 'amount', 'instalment_month', 'instalment_year', 'created_at', 'updated_at'], 'required'],
            [['receiver_id', 'is_paid', 'is_deleted'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['monthly_invoice_number', 'instalment_month', 'instalment_year'], 'string', 'max' => 50],
            [['monthly_invoice_number'], 'unique'],
            [['receiver_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['receiver_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'monthly_invoice_id' => 'Monthly Invoice ID',
            'monthly_invoice_number' => 'Monthly Invoice Number',
            'receiver_id' => 'Receiver ID',
            'amount' => 'Amount',
            'instalment_month' => 'Instalment Month',
            'instalment_year' => 'Instalment Year',
            'is_paid' => 'Is Paid',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceiver()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'receiver_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentReceiveds()
    {
        return $this->hasMany(PaymentReceived::className(), ['monthly_invoice_id' => 'monthly_invoice_id']);
    }
}
