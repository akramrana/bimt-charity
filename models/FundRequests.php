<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fund_requests".
 *
 * @property int $fund_request_id
 * @property string $fund_request_number
 * @property int $request_user_id
 * @property string $request_description
 * @property double $request_amount
 * @property string $file
 * @property int $is_active
 * @property int $is_deleted
 * @property string $created_at
 * @property string $updated_at
 *
 * @property FundRequestStatus[] $fundRequestStatuses
 * @property Users $requestUser
 * @property PaymentRelease[] $paymentReleases
 */
class FundRequests extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fund_requests';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fund_request_number', 'request_user_id', 'request_description', 'request_amount', 'created_at', 'updated_at'], 'required'],
            [['request_user_id', 'is_active', 'is_deleted'], 'integer'],
            [['request_description'], 'string'],
            [['request_amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['fund_request_number'], 'string', 'max' => 50],
            [['file'], 'string', 'max' => 250],
            [['fund_request_number'], 'unique'],
            [['request_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['request_user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fund_request_id' => 'Fund Request ID',
            'fund_request_number' => 'Fund Request Number',
            'request_user_id' => 'Request User ID',
            'request_description' => 'Request Description',
            'request_amount' => 'Request Amount',
            'file' => 'File',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFundRequestStatuses()
    {
        return $this->hasMany(FundRequestStatus::className(), ['fund_request_id' => 'fund_request_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestUser()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'request_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentReleases()
    {
        return $this->hasMany(PaymentRelease::className(), ['fund_request_id' => 'fund_request_id']);
    }
}
