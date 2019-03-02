<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MonthlyInvoice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="monthly-invoice-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'monthly_invoice_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiver_id')->textInput() ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'instalment_month')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'instalment_year')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_paid')->textInput() ?>

    <?= $form->field($model, 'is_deleted')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
