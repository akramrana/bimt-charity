<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FundRequests */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fund-requests-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fund_request_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'request_user_id')->textInput() ?>

    <?= $form->field($model, 'request_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'request_amount')->textInput() ?>

    <?= $form->field($model, 'file')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_active')->textInput() ?>

    <?= $form->field($model, 'is_deleted')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
