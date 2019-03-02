<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MonthlyInvoiceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="monthly-invoice-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'monthly_invoice_id') ?>

    <?= $form->field($model, 'monthly_invoice_number') ?>

    <?= $form->field($model, 'receiver_id') ?>

    <?= $form->field($model, 'amount') ?>

    <?= $form->field($model, 'instalment_month') ?>

    <?php // echo $form->field($model, 'instalment_year') ?>

    <?php // echo $form->field($model, 'is_paid') ?>

    <?php // echo $form->field($model, 'is_deleted') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
