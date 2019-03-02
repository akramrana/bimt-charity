<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MonthlyInvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Monthly Invoices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="monthly-invoice-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Monthly Invoice', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'monthly_invoice_id',
            'monthly_invoice_number',
            'receiver_id',
            'amount',
            'instalment_month',
            //'instalment_year',
            //'is_paid',
            //'is_deleted',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
