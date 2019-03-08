<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentRelease */

$this->title = $model->release_invoice_number;
$this->params['breadcrumbs'][] = ['label' => 'Payment Releases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="box box-primary">

    <div class="box-body">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->payment_release_id], ['class' => 'btn btn-primary']) ?>
            <?=
            Html::a('Delete', ['delete', 'id' => $model->payment_release_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ])
            ?>
        </p>

        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'release_invoice_number',
                [
                    'attribute' => 'fund_request_id',
                    'value' => $model->fundRequest->fund_request_number
                ],
                [
                    'attribute' => 'release_by',
                    'value' => $model->releaseBy->fullname
                ],
                'amount',
                'note:ntext',
                'created_at',
                'updated_at',
            ],
        ])
        ?>

    </div>

</div>