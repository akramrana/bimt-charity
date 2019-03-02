<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\FundRequests */

$this->title = $model->fund_request_id;
$this->params['breadcrumbs'][] = ['label' => 'Fund Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="fund-requests-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->fund_request_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->fund_request_id], [
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
            'fund_request_id',
            'fund_request_number',
            'request_user_id',
            'request_description:ntext',
            'request_amount',
            'file',
            'is_active',
            'is_deleted',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
