<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MonthlyInvoice */

$this->title = $model->monthly_invoice_id;
$this->params['breadcrumbs'][] = ['label' => 'Monthly Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="monthly-invoice-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->monthly_invoice_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->monthly_invoice_id], [
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
            'monthly_invoice_id',
            'monthly_invoice_number',
            'receiver_id',
            'amount',
            'instalment_month',
            'instalment_year',
            'is_paid',
            'is_deleted',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
