<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\DatePickerAsset;
use dosamigos\fileupload\FileUpload;

DatePickerAsset::register($this);

/* @var $this yii\web\View */
/* @var $model app\models\PaymentReceived */
/* @var $form yii\widgets\ActiveForm */
$userPaidInvoiceList = [];
if (!$model->isNewRecord) {
    $userPaidInvoiceList = app\helpers\AppHelper::getUserPaidInvoiceList($model->donated_by);
}else{
    $userPaidInvoiceList = app\helpers\AppHelper::getUserUnpaidInvoiceList($model->donated_by);
}
?>

<div class="payment-received-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'received_invoice_number')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>
        </div>
        <span class="clearfix"></span>
        <div class="col-md-6">
            <?=
            $form->field($model, 'received_date')->textInput([
                'class' => 'form-control datepicker'
            ]);
            ?>

            <?= $form->field($model, 'comments')->textarea(['rows' => 6]) ?>
        </div>
        <span class="clearfix"></span>
        <div class="col-md-6">

            <?=
            $form->field($model, 'has_invoice')->checkbox([
                'onclick' => 'app.showHideMonthlyInvoice()'
            ])
            ?>

            <?php
            $class = 'hidden';
            $class2 = '';
            if (!$model->isNewRecord) {
                if ($model->has_invoice == '1') {
                    $class = '';
                    $class2 = 'hidden';
                }
            }
            ?>
            <div id="monthly-invoice" class="<?= $class; ?>">
                <?=
                $form->field($model, 'monthly_invoice_id')->dropDownList($userPaidInvoiceList, [
                    'prompt' => 'Please Select'
                ])->label('Select Invoice');
                ?>
            </div>
        </div>
        <span class="clearfix"></span>
        <div id="instalment-month-year" class="<?= $class2; ?>">
            <div class="col-md-6">
                <?= $form->field($model, 'amount')->textInput() ?>

                <?=
                $form->field($model, 'currency_id')->dropDownList(app\helpers\AppHelper::getAllCurrency(), [
                    'prompt' => 'Please Select'
                ])
                ?>
            </div>
            <span class="clearfix"></span>
            <div class="col-md-6">
                <?=
                $form->field($model, 'instalment_month')->dropDownList(app\helpers\AppHelper::monthList(), [
                    'prompt' => 'Please Select'
                ])
                ?>
            </div>
            <div class="col-md-6">
                <?=
                $form->field($model, 'instalment_year')->dropDownList(app\helpers\AppHelper::YearsList(), [
                    'prompt' => 'Please Select'
                ])
                ?>
            </div>
        </div>
        <div class="col-md-6">
            <label>
                Upload File
            </label>
            <span class="clearfix"></span>
            <?php
            echo FileUpload::widget([
                'name' => 'PaymentReceived[file_name]',
                'url' => [
                    'upload/common?attribute=PaymentReceived[file_name]'
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
                                            var img = \'<br/><img id="clientImg" class="img-responsive" src="' . yii\helpers\BaseUrl::home() . 'uploads/\'+data.result.files.name+\'" alt="img" style="max-width:512px;"/>\';
                                            $("#image_preview").html(img);
                                            $(".field-paymentreceived-file input[type=hidden]").val(data.result.files.name);
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
                        <br/><img src="<?php echo yii\helpers\BaseUrl::home() ?>uploads/<?php echo $model->file ?>" alt="img" style="max-width:512px;"/>
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
<?php
$this->registerJs("$('.datepicker').datepicker({
       format: 'yyyy-mm-dd',
        autoclose: true,
});", \yii\web\View::POS_END);
