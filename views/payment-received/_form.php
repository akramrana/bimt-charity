<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentReceived */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-received-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'received_invoice_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'donated_by')->textInput() ?>

    <?= $form->field($model, 'received_by')->textInput() ?>

    <?= $form->field($model, 'comments')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'instalment_month')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'instalment_year')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'has_invoice')->textInput() ?>

    <?= $form->field($model, 'monthly_invoice_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'is_deleted')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
