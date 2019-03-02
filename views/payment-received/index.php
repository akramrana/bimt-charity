<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentReceivedSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payment Receiveds';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-received-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Payment Received', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'payment_received_id',
            'received_invoice_number',
            'donated_by',
            'received_by',
            'comments:ntext',
            //'amount',
            //'instalment_month',
            //'instalment_year',
            //'has_invoice',
            //'monthly_invoice_id',
            //'created_at',
            //'updated_at',
            //'is_deleted',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
