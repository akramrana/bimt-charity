<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentReceived */

$this->title = 'Update Payment Received: ' . $model->payment_received_id;
$this->params['breadcrumbs'][] = ['label' => 'Payment Receiveds', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->payment_received_id, 'url' => ['view', 'id' => $model->payment_received_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="payment-received-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
