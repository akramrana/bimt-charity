<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MonthlyInvoice */

$this->title = $model->monthly_invoice_number;
$this->params['breadcrumbs'][] = ['label' => 'Monthly Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="box box-primary">

    <div class="box-body">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->monthly_invoice_id], ['class' => 'btn btn-primary']) ?>
            <?=
            Html::a('Delete', ['delete', 'id' => $model->monthly_invoice_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ])
            ?>
        </p>

        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'monthly_invoice_number',
                [
                    'attribute' => 'receiver_id',
                    'value' => $model->receiver->fullname
                ],
                'amount',
                'instalment_month',
                'instalment_year',
                [
                    'attribute' => 'is_paid',
                    'value' => ($model->is_paid == 1) ? "Yes" : "No"
                ],
                'created_at',
                'updated_at',
            ],
        ])
        ?>

    </div>

</div>
