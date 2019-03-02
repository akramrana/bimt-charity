<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentReceived */

$this->title = $model->payment_received_id;
$this->params['breadcrumbs'][] = ['label' => 'Payment Receiveds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="payment-received-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->payment_received_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->payment_received_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'payment_received_id',
            'received_invoice_number',
            'donated_by',
            'received_by',
            'comments:ntext',
            'amount',
            'instalment_month',
            'instalment_year',
            'has_invoice',
            'monthly_invoice_id',
            'created_at',
            'updated_at',
            'is_deleted',
        ],
    ]) ?>

</div>
