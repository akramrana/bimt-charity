<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payment_release".
 *
 * @property int $payment_release_id
 * @property string $release_invoice_number
 * @property int $fund_request_id
 * @property int $release_by
 * @property double $amount
 * @property string $note
 * @property int $is_deleted
 * @property string $created_at
 * @property string $updated_at
 *
 * @property FundRequests $fundRequest
 * @property Users $releaseBy
 */
class PaymentRelease extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_release';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['release_invoice_number', 'fund_request_id', 'release_by', 'amount', 'created_at', 'updated_at'], 'required'],
            [['fund_request_id', 'release_by', 'is_deleted'], 'integer'],
            [['amount'], 'number'],
            [['note'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['release_invoice_number'], 'string', 'max' => 50],
            [['release_invoice_number'], 'unique'],
            [['fund_request_id'], 'exist', 'skipOnError' => true, 'targetClass' => FundRequests::className(), 'targetAttribute' => ['fund_request_id' => 'fund_request_id']],
            [['release_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['release_by' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'payment_release_id' => 'Payment Release ID',
            'release_invoice_number' => 'Release Invoice Number',
            'fund_request_id' => 'Fund Request',
            'release_by' => 'Release By',
            'amount' => 'Amount',
            'note' => 'Note',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFundRequest()
    {
        return $this->hasOne(FundRequests::className(), ['fund_request_id' => 'fund_request_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReleaseBy()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'release_by']);
    }
}
