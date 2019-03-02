<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentRelease */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-release-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'release_invoice_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fund_request_id')->textInput() ?>

    <?= $form->field($model, 'release_by')->textInput() ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'is_deleted')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
