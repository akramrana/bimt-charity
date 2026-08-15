<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cms".
 *
 * @property int $cms_id
 * @property string $title
 * @property string $content
 * @property int $is_deleted
 */
class Cms extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'content', 'is_deleted'], 'required'],
            [['content'], 'string'],
            [['is_deleted'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cms_id' => 'Cms ID',
            'title' => 'Title',
            'content' => 'Content',
            'is_deleted' => 'Is Deleted',
        ];
    }
}
