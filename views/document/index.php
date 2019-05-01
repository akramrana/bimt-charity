<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Documents';
$this->params['breadcrumbs'][] = $this->title;

$actionBtn = '{view}{update}{delete}';
$allowCreate = true;
if (\Yii::$app->session['__bimtCharityUserRole'] == 3) {
    $actionBtn = '{view}{update}';
} else if (\Yii::$app->session['__bimtCharityUserRole'] == 4) {
    $actionBtn = '{view}';
    $allowCreate = false;
}
?>
<div class="box box-primary">

    <div class="box-body">

        <p>
            <?= ($allowCreate) ? Html::a('Create Documents', ['create'], ['class' => 'btn btn-success']) : "" ?>
        </p>

        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'title',
                [
                    'attribute' => 'file',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::a('Download File', \yii\helpers\BaseUrl::home() . 'uploads/' . $model->file, [
                                    'download' => $model->file,
                                    'title' => $model->title,
                        ]);
                    }
                ],
                [
                    'attribute' => 'user_id',
                    'value' => function($model) {
                        return $model->user->fullname;
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'user_id', app\helpers\AppHelper ::getAllUsers(), ['class' => 'form-control', 'prompt' => 'Filter']),
                ],
                [
                    'class' => 'yii\grid\ActionColumn', 'template' => $actionBtn
                ],
            ],
        ]);
        ?>
    </div>

</div>
