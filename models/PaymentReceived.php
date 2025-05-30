<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payment_received".
 *
 * @property int $payment_received_id
 * @property string $received_invoice_number
 * @property int $donated_by
 * @property int $received_by
 * @property string $comments
 * @property double $amount
 * @property string $instalment_month
 * @property string $instalment_year
 * @property int $has_invoice
 * @property int $monthly_invoice_id
 * @property double $created_at
 * @property double $updated_at
 * @property int $is_deleted
 *
 * @property MonthlyInvoice $monthlyInvoice
 * @property Users $donatedBy
 * @property Users $receivedBy
 */
class PaymentReceived extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'payment_received';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['received_invoice_number', 'donated_by', 'received_by', 'amount', 'created_at', 'updated_at'], 'required'],
            [['donated_by', 'received_by', 'has_invoice', 'monthly_invoice_id', 'is_deleted'], 'integer'],
            [['comments'], 'string'],
            [['amount'], 'number'],
            [['currency_id', 'received_date', 'file'], 'safe'],
            [['received_invoice_number', 'instalment_month', 'instalment_year'], 'string', 'max' => 50],
            [['received_invoice_number'], 'unique'],
            [['monthly_invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => MonthlyInvoice::className(), 'targetAttribute' => ['monthly_invoice_id' => 'monthly_invoice_id']],
            [['donated_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['donated_by' => 'user_id']],
            [['received_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['received_by' => 'user_id']],
            [['instalment_month', 'instalment_year', 'currency_id', 'received_date'], 'required', 'on' => 'add-sadaqa'],
            [['instalment_month', 'instalment_year', 'currency_id', 'received_date'], 'required', 'on' => 'update-sadaqa']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'payment_received_id' => 'Payment Received ID',
            'received_invoice_number' => 'Sadaqah Invoice Number',
            'donated_by' => 'Donated By',
            'received_by' => 'Received By',
            'comments' => 'Comments',
            'amount' => 'Amount',
            'instalment_month' => 'Instalment Month',
            'instalment_year' => 'Instalment Year',
            'has_invoice' => 'Has Invoice',
            'monthly_invoice_id' => 'Monthly Invoice',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
            'currency_id' => 'Currency',
            'received_date' => 'Received Date',
            'file' => 'Proof',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMonthlyInvoice() {
        return $this->hasOne(MonthlyInvoice::className(), ['monthly_invoice_id' => 'monthly_invoice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDonatedBy() {
        return $this->hasOne(Users::className(), ['user_id' => 'donated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceivedBy() {
        return $this->hasOne(Users::className(), ['user_id' => 'received_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency() {
        return $this->hasOne(Currencies::className(), ['currency_id' => 'currency_id']);
    }
}
