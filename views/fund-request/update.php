<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\FundRequests */

$this->title = 'Update Fund Requests: ' . $model->fund_request_id;
$this->params['breadcrumbs'][] = ['label' => 'Fund Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fund_request_id, 'url' => ['view', 'id' => $model->fund_request_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="fund-requests-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
