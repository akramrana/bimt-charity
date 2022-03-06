<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\fileupload\FileUpload;
use app\assets\SelectAsset;

$this->title = 'Send E-mail';
?>
<div class="box box-primary">
    <div class="box-body">
        <div class="documents-form">
            <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <div class="col-md-12">
                    <?=
                    $form->field($model, 'sent_to')->radioList(['A' => 'All', 'S' => 'Selected'], [
                        'onChange' => 'app.handleRadio(this);'
                    ]);
                    ?>
                </div>
                <div class="col-md-12">
                    <?=
                    $form->field($model, 'userIds')->dropDownList(app\helpers\AppHelper::getAllUsersWithEmail(), [
                        'class' => 'select2 form-control',
                        'multiple' => 'multiple',
                    ])
                    ?>
                </div>
                <div class="col-md-12">
                    <?= $form->field($model, 'subject')->textInput() ?>
                </div>
                <div class="col-md-12">
                    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>
                </div>
                <div class="col-md-12">
                    <label>
                        Attachment
                    </label>
                    <span class="clearfix"></span>
                    <?php
                    echo FileUpload::widget([
                        'name' => 'SendMailForm[attachment]',
                        'url' => [
                            'upload/document?attribute=SendMailForm[attachment]'
                        ],
                        'options' => [
                            'tabindex' => 2
                        ],
                        'clientOptions' => [
                            'dataType' => 'json',
                            'maxFileSize' => 2000000,
                        ],
                        'clientEvents' => [
                            'fileuploadprogressall' => "function (e, data) {
                                        var progress = parseInt(data.loaded / data.total * 100, 10);
                                        $('#progress').show();
                                        $('#progress .progress-bar').css(
                                            'width',
                                            progress + '%'
                                        );
                                     }",
                            'fileuploaddone' => 'function (e, data) {
                                        if(data.result.files.error==""){
                                            var img = \'<br/><a download href="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="download">Download Attachment</a>\';
                                            $("#image_preview").html(img);
                                            $(".field-sendmailform-attachment input[type=hidden]").val(data.result.files.name);
                                            $("#progress .progress-bar").attr("style","width: 0%;");
                                            $("#progress").hide();
                                            $("#progress .progress-bar").attr("style","width: 0%;");
                                        }
                                        else{
                                           $("#progress .progress-bar").attr("style","width: 0%;");
                                           $("#progress").hide();
                                           var errorHtm = \'<span style="color:#dd4b39">\'+data.result.files.error+\'</span>\';
                                           $("#image_preview").html(errorHtm);
                                           setTimeout(function(){
                                               $("#image_preview span").remove();
                                           },3000)
                                        }
                                    }',
                        ],
                    ]);
                    ?>
                    <small>Max upload size 1MB</small>

                    <div id="progress" class="progress m-t-xs full progress-small" style="display: none;">
                        <div class="progress-bar progress-bar-success"></div>
                    </div>
                    <div id="image_preview">
                    </div>
                    <?= $form->field($model, 'attachment')->hiddenInput()->label(false) ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>