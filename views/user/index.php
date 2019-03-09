<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
$actionBtn = '{view}{update}{delete}';
$allowActivate = true;
if (\Yii::$app->session['__bimtCharityUserRole'] == 3) {
    $actionBtn = '{view}{update}';
}
else if (\Yii::$app->session['__bimtCharityUserRole'] == 4) {
    $actionBtn = '{view}';
    $allowActivate = false;
}
?>
<div class="box box-primary">

    <div class="box-body">
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a('Create Users', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'label' => 'Image',
                    'value' => function($model) {
                        return \yii\helpers\BaseUrl::home() . 'uploads/' . $model->image;
                    },
                    'format' => ['image', ['width' => '96']],
                    'filter' => false,
                ],
                'fullname',
                //'image',
                'email:email',
                'phone',
                'alt_phone',
                'address:ntext',
                //'batch',
                //'department',
                //'enable_login',
                //'password',
                //'user_type',
                'recurring_amount',
                [
                    'label' => 'Status',
                    'attribute' => 'is_active',
                    'format' => 'raw',
                    'value' => function ($model, $url) use ($allowActivate) {
                        return '<div class="onoffswitch">'
                                . Html::checkbox('onoffswitch', $model->is_active, [
                                    'class' => "onoffswitch-checkbox",
                                    'id' => "myonoffswitch" . $model->user_id,
                                    'onclick' => 'app.changeStatus("user/activate",this,' . $model->user_id . ')',
                                    'disabled' => ($allowActivate)?false:true,
                                ])
                                . '<label class="onoffswitch-label" for="myonoffswitch' . $model->user_id . '"></label></div>';
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'is_active', [1 => 'Active', 0 => 'Inactive'], ['class' => 'form-control', 'prompt' => 'Filter']),
                ],
                //'is_deleted',
                //'created_at',
                //'updated_at',
                ['class' => 'yii\grid\ActionColumn','template' => $actionBtn],
            ],
        ]);
        ?>
    </div>

</div>