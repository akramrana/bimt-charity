<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\fileupload\FileUpload;

/* @var $this yii\web\View */
/* @var $model app\models\Documents */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="documents-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">

            <?= $form->field($model, 'title')->textInput() ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

            <label>
                Upload File
            </label>
            <span class="clearfix"></span>
            <?php
            echo FileUpload::widget([
                'name' => 'Documents[file_name]',
                'url' => [
                    'upload/document?attribute=Documents[file_name]'
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
                                            var img = \'<br/><a download href="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="download">Download File</a>\';
                                            $("#image_preview").html(img);
                                            $(".field-documents-file input[type=hidden]").val(data.result.files.name);
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
                    if ($model->file != "") {
                        ?>
                        <br/><a href="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->file ?>" alt="download">Download File</a>
                        <?php
                    }
                }
                ?>
            </div>

            <?= $form->field($model, 'file')->hiddenInput()->label(false) ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
