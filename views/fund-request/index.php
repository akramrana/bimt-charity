<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FundRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Fund Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fund-requests-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Fund Requests', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'fund_request_id',
            'fund_request_number',
            'request_user_id',
            'request_description:ntext',
            'request_amount',
            //'file',
            //'is_active',
            //'is_deleted',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
