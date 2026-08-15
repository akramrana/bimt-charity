<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "device_tokens".
 *
 * @property int $id
 * @property int $user_id
 * @property string $device_id
 * @property string $push_type
 * @property string $push_token
 * @property string $created_at
 * @property string $updated_at
 */
class DeviceTokens extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'device_tokens';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['device_id', 'push_type', 'push_token'], 'required'],
            [['push_type', 'push_token'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['device_id'], 'string', 'max' => 100],
            [['device_id', 'push_type'], 'unique', 'targetAttribute' => ['device_id', 'push_type']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'device_id' => 'Device ID',
            'push_type' => 'Push Type',
            'push_token' => 'Push Token',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
