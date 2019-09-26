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
    public $password_hash;
    public $confirm_password;
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
            [['fullname', 'email', 'phone', 'password', 'user_type', 'created_at', 'updated_at', 'recurring_amount'], 'required'],
            [['address', 'user_type'], 'string'],
            [['enable_login', 'is_active', 'is_deleted'], 'integer'],
            [['recurring_amount'], 'number'],
            [['created_at', 'updated_at', 'currency_id', 'member_code', 'invited_user_id'], 'safe'],
            [['fullname', 'email', 'phone', 'alt_phone', 'batch', 'department'], 'string', 'max' => 50],
            [['image'], 'string', 'max' => 250],
            ['email', 'email'],
            ['email', 'checkUniqueEmail'],
            [['password_hash', 'confirm_password'], 'required', 'on' => 'create'],
            ['confirm_password', 'compare', 'compareAttribute' => 'password_hash', 'message' => Yii::t('yii', 'Confirm Password must be equal to "Password"')],
            [['password_hash'], 'string', 'min' => 6],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'member_code' => 'Member ID',
            'fullname' => 'Name',
            'image' => 'Image',
            'email' => 'Email',
            'phone' => 'Phone',
            'alt_phone' => 'Alt Phone',
            'address' => 'Address',
            'batch' => 'Batch',
            'department' => 'Department',
            'enable_login' => 'Enable Login',
            'password' => 'Password',
            'password_hash' => 'Password',
            'confirm_password' => 'Confirm Password',
            'user_type' => 'User Type',
            'recurring_amount' => 'Donation Amount Per Month',
            'invited_user_id' => 'Invited By',
            'is_active' => 'Active Status',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'currency_id' => 'Currency',
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
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency()
    {
        return $this->hasOne(Currencies::className(), ['currency_id' => 'currency_id']);
    }
    
    public function checkUniqueEmail($attribute, $params) {
        $query = Users::find()
                ->where(['email' => $this->email, 'is_deleted' => 0]);
        if (isset($this->user_id) && $this->user_id != "") {
            $query->andWhere(['<>', 'user_id', $this->user_id]);
        }
        $model = $query->one();
        if (!empty($model)) {
            $this->addError($attribute, Yii::t('app', 'This email has already been taken.'));
        }
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvitedBy()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'invited_user_id']);
    }
}
