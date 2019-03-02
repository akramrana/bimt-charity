<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentRelease */

$this->title = 'Create Payment Release';
$this->params['breadcrumbs'][] = ['label' => 'Payment Releases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-release-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
