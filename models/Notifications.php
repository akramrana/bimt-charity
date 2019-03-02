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
            [['created_at'], 'safe'],
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
            'comments' => 'Comments',
            'created_at' => 'Created At',
            'is_deleted' => 'Is Deleted',
        ];
    }
}
