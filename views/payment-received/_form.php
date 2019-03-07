<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentReceived */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-received-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'received_invoice_number')->textInput(['maxlength' => true, 'readonly' => 'readonly']) ?>
        </div>
        <span class="clearfix"></span>
        <div class="col-md-6">
            <?=
            $form->field($model, 'donated_by')->dropDownList(app\helpers\AppHelper::getAllUsers(), [
                'prompt' => 'Please Select'
            ])
            ?>
        </div>
        <div class="col-md-6">
            <?=
            $form->field($model, 'received_by')->dropDownList(app\helpers\AppHelper::getAllUsers(), [
                'prompt' => 'Please Select'
            ])
            ?>
        </div>
        <span class="clearfix"></span>
        <div class="col-md-6">
            <?= $form->field($model, 'comments')->textarea(['rows' => 6]) ?>
        </div>
        <span class="clearfix"></span>
        <div class="col-md-6">

            <?= $form->field($model, 'has_invoice')->checkbox([
                'onclick' => 'app.showHideMonthlyInvoice()'
            ]) ?>

            <?php
            $class = 'hidden';
            $class2 = '';
            if(!$model->isNewRecord){
                if($model->has_invoice=='1'){
                    $class = '';
                    $class2 = 'hidden';
                }
            }
            ?>
            <div id="monthly-invoice" class="<?=$class;?>">
                <?=
                $form->field($model, 'monthly_invoice_id')->dropDownList(app\helpers\AppHelper::getPaidInvoiceList(), [
                    'prompt' => 'Please Select'
                ])->label('Select Invoice');
                ?>
            </div>
        </div>
        <span class="clearfix"></span>
        <div id="instalment-month-year" class="<?=$class2;?>">
            <div class="col-md-6">
                <?= $form->field($model, 'amount')->textInput() ?>
            </div>
            <span class="clearfix"></span>
            <div class="col-md-6">
                <?=
                $form->field($model, 'instalment_month')->dropDownList(app\helpers\AppHelper::monthList(), [
                    'prompt' => 'Please Select'
                ])
                ?>
            </div>
            <div class="col-md-6">
                <?=
                $form->field($model, 'instalment_year')->dropDownList(app\helpers\AppHelper::YearsList(), [
                    'prompt' => 'Please Select'
                ])
                ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
