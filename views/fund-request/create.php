<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\FundRequests */

$this->title = 'Create Fund Requests';
$this->params['breadcrumbs'][] = ['label' => 'Fund Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
