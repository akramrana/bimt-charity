<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\fileupload\FileUpload;

/* @var $this yii\web\View */
/* @var $model app\models\FundRequests */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fund-requests-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'fund_request_number')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>

            <?=
            $form->field($model, 'request_user_id')->dropDownList(app\helpers\AppHelper::getAllUsers(), [
                'prompt' => 'Please Select'
            ])
            ?>
        </div>
        <span class="clearfix"></span>
        <div class="col-md-6">
            <?= $form->field($model, 'request_description')->textarea(['rows' => 6]) ?>
        </div>
        <span class="clearfix"></span>
        <div class="col-md-6">
            <?= $form->field($model, 'request_amount')->textInput() ?>

            <label>
                Document(if any)
            </label>
            <span class="clearfix"></span>
            <?php
            echo FileUpload::widget([
                'name' => 'FundRequests[file]',
                'url' => [
                    'upload/document?attribute=FundRequests[file]'
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
                                            var file = \'<br/><a download href="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'">Download Document</a>\';
                                            $("#image_preview").html(file);
                                            $(".field-fundrequests-file input[type=hidden]").val(data.result.files.name);
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

            <div id="progress" class="progress m-t-xs full progress-small" style="display: none;">
                <div class="progress-bar progress-bar-success"></div>
            </div>
            <div id="image_preview">
                <?php
                if (!$model->isNewRecord) {
                    if ($model->file != "") {
                        ?>
                        <br/><a download href="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->file ?>">Download Document</a>
                        <?php
                    }
                }
                ?>
            </div>

            <?php echo $form->field($model, 'file')->hiddenInput()->label(false); ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
