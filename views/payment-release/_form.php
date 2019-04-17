<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentRelease */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-release-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'release_invoice_number')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>
        </div>
        <span class="clearfix"></span>
        <div class="col-md-6">
            <?=
            $form->field($model, 'fund_request_id')->dropDownList(app\helpers\AppHelper::getApprovedFundRequest(), [
                'prompt' => 'Please Select'
            ])
            ?>

            <?= $form->field($model, 'amount')->textInput() ?>
        </div>
        <div class="col-md-6">
            <?=
            $form->field($model, 'release_by')->dropDownList(app\helpers\AppHelper::getAllUsers(), [
                'prompt' => 'Please Select'
            ])
            ?>
            
            <?=
            $form->field($model, 'currency_id')->dropDownList(app\helpers\AppHelper::getAllCurrency(), [
                'prompt' => 'Please Select'
            ])
            ?>
        </div>
        <span class="clearfix"></span>
        <div class="col-md-6">
        <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
        </div>
    </div>

    <div class="form-group">
<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

<?php ActiveForm::end(); ?>

</div>
