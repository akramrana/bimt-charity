<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Notifications */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notifications-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'type')->dropDownList([ 'FR' => 'FR', 'MI' => 'MI', 'PREC' => 'PREC', 'PREL' => 'PREL', 'GE' => 'GE', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'type_id')->textInput() ?>

    <?= $form->field($model, 'comments')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'is_deleted')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
