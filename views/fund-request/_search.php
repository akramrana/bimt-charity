<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FundRequestSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fund-requests-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'fund_request_id') ?>

    <?= $form->field($model, 'fund_request_number') ?>

    <?= $form->field($model, 'request_user_id') ?>

    <?= $form->field($model, 'request_description') ?>

    <?= $form->field($model, 'request_amount') ?>

    <?php // echo $form->field($model, 'file') ?>

    <?php // echo $form->field($model, 'is_active') ?>

    <?php // echo $form->field($model, 'is_deleted') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
