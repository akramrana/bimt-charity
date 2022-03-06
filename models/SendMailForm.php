<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Description of SendMailForm
 *
 * @author akram
 */
class SendMailForm extends Model
{
    //put your code here
    public $sent_to;
    public $userIds;
    public $subject;
    public $message;
    public $attachment;
    public $verifyCode;
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['subject', 'message', 'sent_to'], 'required'],
            [['attachment'], 'string', 'max' => 250],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'sent_to' => 'Sent To',
            'userIds' => 'Recipient(s)',
            'subject' => 'Subject',
            'message' => 'Message',
            'Attachment' => 'attachment',
        ];
    }
}
