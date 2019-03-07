<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MonthlyInvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Monthly Invoices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-primary">

    <div class="box-body">

        <p>
            <?= Html::a('Create Monthly Invoice', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'monthly_invoice_number',
                [
                    'attribute' => 'receiver_id',
                    'value' => function($model) {
                        return $model->receiver->fullname;
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'receiver_id', app\helpers\AppHelper ::getAllUsers(), ['class' => 'form-control', 'prompt' => 'Filter']),
                ],
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
                    'attribute' => 'is_paid',
                    'value' => function($model) {
                        return ($model->is_paid == '1') ? "Yes" : "No";
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'is_paid', [1 => 'Yes', 0 => 'No'], ['class' => 'form-control', 'prompt' => 'Filter']),
                ],
                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]);
        ?>

    </div>

</div>
