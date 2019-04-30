<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "documents".
 *
 * @property int $document_id
 * @property string $file
 * @property int $user_id
 * @property string $created_at
 * @property int $is_deleted
 *
 * @property Users $user
 */
class Documents extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file', 'user_id', 'created_at', 'title'], 'required'],
            [['user_id', 'is_deleted'], 'integer'],
            [['created_at','description','title'], 'safe'],
            [['file'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'document_id' => 'Document ID',
            'file' => 'File',
            'title' => 'Title',
            'description' => 'Description',
            'user_id' => 'Uploaded By',
            'created_at' => 'Created At',
            'is_deleted' => 'Is Deleted',
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
