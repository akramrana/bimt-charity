<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $user_id
 * @property string $fullname
 * @property string $image
 * @property string $email
 * @property string $phone
 * @property string $alt_phone
 * @property string $address
 * @property string $batch
 * @property string $department
 * @property int $enable_login
 * @property int $password
 * @property string $user_type
 * @property double $recurring_amount
 * @property int $is_active
 * @property int $is_deleted
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Expenses[] $expenses
 * @property FundRequestStatus[] $fundRequestStatuses
 * @property FundRequests[] $fundRequests
 * @property LoginHistory[] $loginHistories
 * @property MonthlyInvoice[] $monthlyInvoices
 * @property PaymentReceived[] $paymentReceiveds
 * @property PaymentReceived[] $paymentReceiveds0
 * @property PaymentRelease[] $paymentReleases
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fullname', 'email', 'phone', 'password', 'user_type', 'created_at', 'updated_at'], 'required'],
            [['address', 'user_type'], 'string'],
            [['enable_login', 'password', 'is_active', 'is_deleted'], 'integer'],
            [['recurring_amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['fullname', 'email', 'phone', 'alt_phone', 'batch', 'department'], 'string', 'max' => 50],
            [['image'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'fullname' => 'Fullname',
            'image' => 'Image',
            'email' => 'Email',
            'phone' => 'Phone',
            'alt_phone' => 'Alt Phone',
            'address' => 'Address',
            'batch' => 'Batch',
            'department' => 'Department',
            'enable_login' => 'Enable Login',
            'password' => 'Password',
            'user_type' => 'User Type',
            'recurring_amount' => 'Recurring Amount',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpenses()
    {
        return $this->hasMany(Expenses::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFundRequestStatuses()
    {
        return $this->hasMany(FundRequestStatus::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFundRequests()
    {
        return $this->hasMany(FundRequests::className(), ['request_user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoginHistories()
    {
        return $this->hasMany(LoginHistory::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMonthlyInvoices()
    {
        return $this->hasMany(MonthlyInvoice::className(), ['receiver_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentReceiveds()
    {
        return $this->hasMany(PaymentReceived::className(), ['donated_by' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentReceiveds0()
    {
        return $this->hasMany(PaymentReceived::className(), ['received_by' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentReleases()
    {
        return $this->hasMany(PaymentRelease::className(), ['release_by' => 'user_id']);
    }
}
