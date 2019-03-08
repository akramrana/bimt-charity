<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FundRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Fund Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-primary">

    <div class="box-body">

        <p>
            <?= Html::a('Create Fund Requests', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'fund_request_number',
                //'request_user_id',
                [
                    'attribute' => 'request_user_id',
                    'value' => function($model) {
                        return $model->requestUser->fullname;
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'request_user_id', app\helpers\AppHelper ::getAllUsers(), ['class' => 'form-control', 'prompt' => 'Filter']),
                ],
                'request_description:ntext',
                'request_amount',
                //'file',
               [
                    'label' => 'Status',
                    'attribute' => 'is_active',
                    'format' => 'raw',
                    'value' => function ($model, $url) {
                        return '<div class="onoffswitch">'
                                . Html::checkbox('onoffswitch', $model->is_active, [
                                    'class' => "onoffswitch-checkbox",
                                    'id' => "myonoffswitch" . $model->fund_request_id,
                                    'onclick' => 'app.changeStatus("fund-request/activate",this,' . $model->fund_request_id . ')',
                                ])
                                . '<label class="onoffswitch-label" for="myonoffswitch' . $model->fund_request_id . '"></label></div>';
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'is_active', [1 => 'Active', 0 => 'Inactive'], ['class' => 'form-control', 'prompt' => 'Filter']),
                ],
                //'is_deleted',
                //'created_at',
                //'updated_at',
                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]);
        ?>
    </div>

</div>
