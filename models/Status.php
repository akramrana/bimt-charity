<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "status".
 *
 * @property int $status_id
 * @property string $name
 * @property int $is_deleted
 *
 * @property FundRequestStatus[] $fundRequestStatuses
 */
class Status extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['is_deleted'], 'integer'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'status_id' => 'Status ID',
            'name' => 'Name',
            'is_deleted' => 'Is Deleted',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFundRequestStatuses()
    {
        return $this->hasMany(FundRequestStatus::className(), ['status_id' => 'status_id']);
    }
}
