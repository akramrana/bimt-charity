<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MonthlyInvoice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="monthly-invoice-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'monthly_invoice_number')->textInput(['maxlength' => true,'readonly' => 'readonly']) ?>
        </div>
        <span class="clearfix"></span>
        <div class="col-md-6">
            <?= $form->field($model, 'receiver_id')->dropDownList(app\helpers\AppHelper::getAllUsers(),[
                'prompt' => 'Please Select'
            ]) ?>
            <?= $form->field($model, 'instalment_month')->dropDownList(app\helpers\AppHelper::monthList(),[
                'prompt' => 'Please Select'
            ]) ?>
            
            <?=
            $form->field($model, 'currency_id')->dropDownList(app\helpers\AppHelper::getAllCurrency(), [
                'prompt' => 'Please Select'
            ])
            ?>
            
            <?= $form->field($model, 'is_paid')->checkbox() ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'amount')->textInput() ?>
            <?= $form->field($model, 'instalment_year')->dropDownList(app\helpers\AppHelper::YearsList(),[
                'prompt' => 'Please Select'
            ]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
