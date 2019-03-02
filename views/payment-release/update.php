<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentRelease */

$this->title = 'Update Payment Release: ' . $model->payment_release_id;
$this->params['breadcrumbs'][] = ['label' => 'Payment Releases', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->payment_release_id, 'url' => ['view', 'id' => $model->payment_release_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="payment-release-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
