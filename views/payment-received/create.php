<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentReceived */

$this->title = 'Create Payment Received';
$this->params['breadcrumbs'][] = ['label' => 'Payment Receiveds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-received-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
