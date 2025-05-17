<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentReceived */

$this->title = $model->received_invoice_number;
$this->params['breadcrumbs'][] = ['label' => 'Sadaqah', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$allowUpdate = false;
$allowDelete = false;
$allowSendMail = false;
if (\Yii::$app->session['__bimtCharityUserRole'] == 1) {
    $allowUpdate = true;
    $allowDelete = true;
    $allowSendMail = true;
}
else if (\Yii::$app->session['__bimtCharityUserRole'] == 2) {
    $allowUpdate = true;
    $allowDelete = true;
    $allowSendMail = true;
}
else if (\Yii::$app->session['__bimtCharityUserRole'] == 3) {
    $allowUpdate = true;
    $allowSendMail = true;
}
?>
<div class="box box-primary">

    <div class="box-body">

        <p>
            <?= ($allowUpdate)?Html::a('Update', ['update', 'id' => $model->payment_received_id], ['class' => 'btn btn-primary']):"" ?>
            <?=
            ($allowDelete)?Html::a('Delete', ['delete', 'id' => $model->payment_received_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]):""
            ?>
            <?= ($allowSendMail)?Html::a('Send Mail', ['send-mail', 'id' => $model->payment_received_id], ['class' => 'btn btn-info pull-right']):"" ?>
        </p>

        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'received_invoice_number',
                [
                    'attribute' => 'donated_by',
                    'value' => $model->donatedBy->fullname
                ],
                [
                    'attribute' => 'received_by',
                    'value' => $model->receivedBy->fullname
                ],
                'comments:ntext',
                'amount',
                'instalment_month',
                'instalment_year',
                //'has_invoice',
                [
                    'attribute' => 'has_invoice',
                    'value' => ($model->has_invoice == 1) ? "Yes" : "No"
                ],
                [
                    'attribute' => 'monthly_invoice_id',
                    'value' => !empty($model->monthlyInvoice)?$model->monthlyInvoice->monthly_invoice_number:""
                ],
                'currency.code',
                'received_date',
                'created_at',
                'updated_at',
                [
                    'label' => 'Proof',
                    'value' => \yii\helpers\BaseUrl::home() . 'uploads/' . $model->file,
                    'format' => ['image', ['width' => '256']],
                ],
            ],
        ])
        ?>

    </div>

</div>
