<?php
namespace app\helpers;
use Yii;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AppHelper
 *
 * @author akram
 */
class AppHelper {
    //put your code here
    static function getUserTypeName($type)
    {
        $types = ['S' => 'Super Admin', 'A' => 'Admin', 'M' => 'Moderator', 'G' => 'General'];
        return $types[$type];
    }
    
    static function addActivity($type,$type_id,$msg)
    {
        $model = new \app\models\Notifications();
        $model->type = $type;
        $model->type_id = $type_id;
        $model->comments = $msg;
        $model->added_by = Yii::$app->user->identity->user_id;
        $model->created_at = date("Y-m-d H:i:s");
        $model->save();
    }
    
    static function getAllUsers()
    {
        $models = \app\models\User::find()->where(['is_deleted' => 0])->all();
        $list = \yii\helpers\ArrayHelper::map($models, 'user_id', 'fullname');
        return $list;
    }
}
