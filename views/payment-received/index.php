<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentReceivedSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sadaqah';
$this->params['breadcrumbs'][] = $this->title;
$allowCreate = true;
$actionBtn = '{view}{update}{delete}';
if (\Yii::$app->session['__bimtCharityUserRole'] == 3) {
    $actionBtn = '{view}{update}';
} else if (\Yii::$app->session['__bimtCharityUserRole'] == 4) {
    $actionBtn = '{view}';
    $allowCreate = true;
}
?>
<div class="box box-primary">

    <div class="box-body">

        <p>
            <?= ($allowCreate) ? Html::a('Add Sadaqah', ['create'], ['class' => 'btn btn-success']) : "" ?>
        </p>

        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'received_invoice_number',
                [
                    'attribute' => 'donated_by',
                    'value' => function($model) {
                        return $model->donatedBy->fullname;
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'donated_by', app\helpers\AppHelper ::getAllUsers(), ['class' => 'form-control', 'prompt' => 'Filter']),
                ],
                [
                    'attribute' => 'received_by',
                    'value' => function($model) {
                        return $model->receivedBy->fullname;
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'received_by', app\helpers\AppHelper ::getAllUsers(), ['class' => 'form-control', 'prompt' => 'Filter']),
                ],
                'comments:ntext',
                'amount',
                [
                    'attribute' => 'instalment_month',
                    'filter' => Html::activeDropDownList($searchModel, 'instalment_month', app\helpers\AppHelper ::monthList(), ['class' => 'form-control', 'prompt' => 'Filter']),
                ],
                [
                    'attribute' => 'instalment_year',
                    'filter' => Html::activeDropDownList($searchModel, 'instalment_year', app\helpers\AppHelper ::YearsList(), ['class' => 'form-control', 'prompt' => 'Filter']),
                ],
                [
                    'attribute' => 'has_invoice',
                    'value' => function($model) {
                        return ($model->has_invoice == '1') ? "Yes" : "No";
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'has_invoice', [1 => 'Yes', 0 => 'No'], ['class' => 'form-control', 'prompt' => 'Filter']),
                ],
                'currency.code',
                [
                    'attribute' => 'monthly_invoice_number',
                    'value' => function($model){
                        return !empty($model->monthlyInvoice) ? $model->monthlyInvoice->monthly_invoice_number : "";
                    }
                ],
                //'monthly_invoice_id',
                //'created_at',
                //'updated_at',
                //'is_deleted',
                ['class' => 'yii\grid\ActionColumn', 'template' => $actionBtn],
            ],
        ]);
        ?>
    </div>

</div>
