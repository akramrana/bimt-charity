<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "login_history".
 *
 * @property int $login_history_id
 * @property int $user_id
 * @property string $datetime
 *
 * @property Users $user
 */
class LoginHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'login_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'datetime'], 'required'],
            [['user_id'], 'integer'],
            [['datetime'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'login_history_id' => 'Login History ID',
            'user_id' => 'User ID',
            'datetime' => 'Datetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
    }
}
