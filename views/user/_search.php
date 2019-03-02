<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'fullname') ?>

    <?= $form->field($model, 'image') ?>

    <?= $form->field($model, 'email') ?>

    <?= $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'alt_phone') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'batch') ?>

    <?php // echo $form->field($model, 'department') ?>

    <?php // echo $form->field($model, 'enable_login') ?>

    <?php // echo $form->field($model, 'password') ?>

    <?php // echo $form->field($model, 'user_type') ?>

    <?php // echo $form->field($model, 'recurring_amount') ?>

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
