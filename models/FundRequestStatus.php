<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fund_request_status".
 *
 * @property int $fund_request_status_id
 * @property int $fund_request_id
 * @property int $status_id
 * @property int $user_id
 * @property int $comments
 * @property string $created_at
 *
 * @property FundRequests $fundRequest
 * @property Status $status
 * @property Users $user
 */
class FundRequestStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fund_request_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fund_request_id', 'status_id', 'user_id', 'created_at'], 'required'],
            [['fund_request_id', 'status_id', 'user_id'], 'integer'],
            [['created_at'], 'safe'],
            [['fund_request_id'], 'exist', 'skipOnError' => true, 'targetClass' => FundRequests::className(), 'targetAttribute' => ['fund_request_id' => 'fund_request_id']],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::className(), 'targetAttribute' => ['status_id' => 'status_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fund_request_status_id' => 'Fund Request Status ID',
            'fund_request_id' => 'Fund Request ID',
            'status_id' => 'Status ID',
            'user_id' => 'User ID',
            'comments' => 'Comments',
            'created_at' => 'Created At',
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
    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['status_id' => 'status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }
}
