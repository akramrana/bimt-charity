<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MonthlyInvoice */

$this->title = 'Create Monthly Invoice';
$this->params['breadcrumbs'][] = ['label' => 'Monthly Invoices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="monthly-invoice-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
