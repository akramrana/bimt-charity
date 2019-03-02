<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MonthlyInvoice */

$this->title = 'Update Monthly Invoice: ' . $model->monthly_invoice_id;
$this->params['breadcrumbs'][] = ['label' => 'Monthly Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->monthly_invoice_id, 'url' => ['view', 'id' => $model->monthly_invoice_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="monthly-invoice-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
