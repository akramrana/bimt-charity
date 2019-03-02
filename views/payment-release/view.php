<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentRelease */

$this->title = $model->payment_release_id;
$this->params['breadcrumbs'][] = ['label' => 'Payment Releases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="payment-release-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->payment_release_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->payment_release_id], [
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
            'payment_release_id',
            'release_invoice_number',
            'fund_request_id',
            'release_by',
            'amount',
            'note:ntext',
            'is_deleted',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
