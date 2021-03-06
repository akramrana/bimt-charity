<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Expenses */

$this->title = $model->purpose;
$this->params['breadcrumbs'][] = ['label' => 'Expenses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$allowUpdate = false;
$allowDelete = false;
if (\Yii::$app->session['__bimtCharityUserRole'] == 1) {
    $allowUpdate = true;
    $allowDelete = true;
}
else if (\Yii::$app->session['__bimtCharityUserRole'] == 2) {
    $allowUpdate = true;
    $allowDelete = true;
}
else if (\Yii::$app->session['__bimtCharityUserRole'] == 3) {
    $allowUpdate = true;
}
?>
<div class="box box-primary">

    <div class="box-body">

        <p>
            <?= ($allowUpdate)?Html::a('Update', ['update', 'id' => $model->expense_id], ['class' => 'btn btn-primary']):"" ?>
            <?=
            ($allowDelete)?Html::a('Delete', ['delete', 'id' => $model->expense_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]):""
            ?>
        </p>

        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'user_id',
                    'value' => ($model->user->fullname)
                ],
                'purpose:ntext',
                'amount',
                'currency.code',
                'created_at',
                'updated_at',
            ],
        ])
        ?>

    </div>

</div>
