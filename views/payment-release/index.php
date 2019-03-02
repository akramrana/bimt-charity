<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentReleaseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payment Releases';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-release-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Payment Release', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'payment_release_id',
            'release_invoice_number',
            'fund_request_id',
            'release_by',
            'amount',
            //'note:ntext',
            //'is_deleted',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
