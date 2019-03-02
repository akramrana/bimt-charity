<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentReleaseSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-release-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'payment_release_id') ?>

    <?= $form->field($model, 'release_invoice_number') ?>

    <?= $form->field($model, 'fund_request_id') ?>

    <?= $form->field($model, 'release_by') ?>

    <?= $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'is_deleted') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
