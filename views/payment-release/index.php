<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentReleaseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payment Releases';
$this->params['breadcrumbs'][] = $this->title;
$actionBtn = '{view}{update}{delete}';
if (\Yii::$app->session['__bimtCharityUserRole'] == 3) {
    $actionBtn = '{view}{update}';
}
else if (\Yii::$app->session['__bimtCharityUserRole'] == 4) {
    $actionBtn = '{view}';
}
?>
<div class="box box-primary">

    <div class="box-body">

        <p>
            <?= Html::a('Create Payment Release', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'release_invoice_number',
                [
                    'attribute' => 'fund_request_id',
                    'value' => function($model) {
                        return $model->fundRequest->fund_request_number;
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'fund_request_id', app\helpers\AppHelper ::getApprovedFundRequest(), ['class' => 'form-control', 'prompt' => 'Filter']),
                ],
                [
                    'attribute' => 'release_by',
                    'value' => function($model) {
                        return $model->releaseBy->fullname;
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'release_by', app\helpers\AppHelper ::getAllUsers(), ['class' => 'form-control', 'prompt' => 'Filter']),
                ],
                'amount',
                'note:ntext',
                //'is_deleted',
                //'created_at',
                //'updated_at',
                ['class' => 'yii\grid\ActionColumn','template' => $actionBtn],
            ],
        ]);
        ?>
    </div>

</div>
