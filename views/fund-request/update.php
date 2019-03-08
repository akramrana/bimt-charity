<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\FundRequests */

$this->title = 'Update Fund Requests: ' . $model->fund_request_number;
$this->params['breadcrumbs'][] = ['label' => 'Fund Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fund_request_id, 'url' => ['view', 'id' => $model->fund_request_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="box box-primary">

    <div class="box-body">

        <?=
        $this->render('_form', [
            'model' => $model,
        ])
        ?>

    </div>

</div>
