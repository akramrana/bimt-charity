<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\fileupload\FileUpload;
/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'fullname')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'alt_phone')->textInput(['maxlength' => true]) ?>
            
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'address')->textarea(['rows' => 6]) ?>
        </div>
        <span class="clearfix"></span>
        <div class="col-md-6">
            <?= $form->field($model, 'batch')->textInput(['maxlength' => true]) ?>
            
            <?= $form->field($model, 'password_hash')->passwordInput() ?>
            
            <?=
            $form->field($model, 'user_type')->dropDownList(['S' => 'Super Admin', 'A' => 'Admin', 'M' => 'Moderator', 'G' => 'General',], [
                'prompt' => 'Please Select'
            ])
            ?>
            
            
            <label>
                Image
            </label>
            <span class="clearfix"></span>
            <?php
            echo FileUpload::widget([
                'name' => 'Users[image]',
                'url' => [
                    'upload/common?attribute=Users[image]'
                ],
                'options' => [
                    'accept' => 'image/*',
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
                                            var img = \'<br/><img id="clientImg" class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="max-width:512px;"/>\';
                                            $("#image_preview").html(img);
                                            $(".field-users-image input[type=hidden]").val(data.result.files.name);
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
                <?php
                if (!$model->isNewRecord) {
                    if ($model->image != "") {
                        ?>
                        <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->image ?>" alt="img" style="max-width:512px;"/>
                        <?php
                    }
                }
                ?>
            </div>

            <?php echo $form->field($model, 'image')->hiddenInput()->label(false); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'department')->textInput(['maxlength' => true]) ?>
            
            <?= $form->field($model, 'confirm_password')->passwordInput() ?>
            
            <?= $form->field($model, 'recurring_amount')->textInput() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
