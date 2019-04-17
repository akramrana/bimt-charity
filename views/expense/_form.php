<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Expenses */
/* @var $form yii\widgets\ActiveForm */
if($model->isNewRecord){
    $model->currency_id = 13;
}
?>

<div class="expenses-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'purpose')->textarea(['rows' => 6]) ?>

            <?= $form->field($model, 'amount')->textInput() ?>
            
            <?=
            $form->field($model, 'currency_id')->dropDownList(app\helpers\AppHelper::getAllCurrency(), [
                'prompt' => 'Please Select'
            ])
            ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
