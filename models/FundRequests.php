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
    public static function tableName() {
        return 'fund_requests';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['fund_request_number', 'request_user_id', 'request_description', 'request_amount', 'created_at', 'updated_at'], 'required'],
            [['request_user_id', 'is_active', 'is_deleted'], 'integer'],
            [['request_description'], 'string'],
            [['request_amount'], 'number'],
            [['created_at', 'updated_at', 'currency_id', 'title', 'reason', 'receiver_contact_details', 'investigation_information', 'fund_receiver_account_details', 'additional_information'], 'safe'],
            [['fund_request_number'], 'string', 'max' => 50],
            [['file'], 'string', 'max' => 250],
            [['fund_request_number'], 'unique'],
            [['request_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['request_user_id' => 'user_id']],
            [['title', 'reason', 'receiver_contact_details', 'investigation_information'], 'required', 'on' => 'create'],
            [['title', 'reason', 'receiver_contact_details', 'investigation_information'], 'required', 'on' => 'update'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'fund_request_id' => 'Fund Request ID',
            'fund_request_number' => 'Fund Request Number',
            'request_user_id' => 'Request By',
            'request_description' => 'Description',
            'request_amount' => 'Amount',
            'file' => 'Document(if any)',
            'is_active' => 'Active Status',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'currency_id' => 'Currency',
            'title' => 'Headline',
            'reason' => 'Why I am submitting this Fund Request?',
            'receiver_contact_details' => 'Contact details of fund receiver',
            'investigation_information' => 'Details information for investigation',
            'fund_receiver_account_details' => 'Account Details of fund receiver',
            'additional_information' => 'Additional information'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFundRequestStatuses() {
        return $this->hasMany(FundRequestStatus::className(), ['fund_request_id' => 'fund_request_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequestUser() {
        return $this->hasOne(Users::className(), ['user_id' => 'request_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentReleases() {
        return $this->hasMany(PaymentRelease::className(), ['fund_request_id' => 'fund_request_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency() {
        return $this->hasOne(Currencies::className(), ['currency_id' => 'currency_id']);
    }

}
