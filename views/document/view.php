<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Documents */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$allowUpdate = false;
$allowDelete = false;
if (\Yii::$app->session['__bimtCharityUserRole'] == 1) {
    $allowUpdate = true;
    $allowDelete = true;
} else if (\Yii::$app->session['__bimtCharityUserRole'] == 2) {
    $allowUpdate = true;
    $allowDelete = true;
} else if (\Yii::$app->session['__bimtCharityUserRole'] == 3) {
    $allowUpdate = true;
}
?>
<div class="box box-primary">

    <div class="box-body">

        <p>
            <?= ($allowUpdate) ? Html::a('Update', ['update', 'id' => $model->document_id], ['class' => 'btn btn-primary']) : "" ?>
            <?=
            ($allowDelete) ? Html::a('Delete', ['delete', 'id' => $model->document_id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]) : ""
            ?>
        </p>

        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'title',
                'description',
                [
                    'attribute' => 'file',
                    'value' => Html::a('Download File', \yii\helpers\BaseUrl::home() . 'uploads/' . $model->file, [
                        'download' => $model->file,
                        'title' => $model->title,
                    ]),
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'user_id',
                    'value' => ($model->user->fullname)
                ],
                'created_at',
            ],
        ])
        ?>

    </div>
</div>
