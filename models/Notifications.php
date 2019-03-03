<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notifications".
 *
 * @property int $notification_id
 * @property string $type
 * @property int $type_id
 * @property string $comments
 * @property string $created_at
 * @property int $is_deleted
 */
class Notifications extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notifications';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'comments'], 'string'],
            [['type_id', 'is_deleted'], 'integer'],
            [['comments', 'created_at'], 'required'],
            [['created_at','added_by'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'notification_id' => 'Notification ID',
            'type' => 'Type',
            'type_id' => 'Type ID',
            'comments' => 'Log Message',
            'created_at' => 'Time',
            'is_deleted' => 'Is Deleted',
            'added_by' => 'Added By',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['user_id' => 'added_by']);
    }
}
