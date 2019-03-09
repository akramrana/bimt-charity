<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Users */

$this->title = $model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
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
            <?= ($allowUpdate)?Html::a('Update', ['update', 'id' => $model->user_id], ['class' => 'btn btn-primary']):"" ?>
            <?=
            ($allowDelete)?Html::a('Delete', ['delete', 'id' => $model->user_id], [
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
                'fullname',
                [
                    'label' => 'Image',
                    'value' => \yii\helpers\BaseUrl::home() . 'uploads/' . $model->image,
                    'format' => ['image', ['width' => '96']],
                ],
                'email:email',
                'phone',
                'alt_phone',
                'address:ntext',
                'batch',
                'department',
                //'enable_login',
                [
                    'attribute' => 'user_type',
                    'value' => app\helpers\AppHelper::getUserTypeName($model->user_type)
                ],
                'recurring_amount',
                [
                    'attribute' => 'is_active',
                    'value' => ($model->is_active == 1) ? "Yes" : "No"
                ],
                'created_at',
                'updated_at',
            ],
        ])
        ?>

    </div>

</div>
