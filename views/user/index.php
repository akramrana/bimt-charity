<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Members';
$this->params['breadcrumbs'][] = $this->title;
$actionBtn = '{view}{update}{reset}{delete}{push}';
$allowActivate = true;
$allowCreate = true;
if (\Yii::$app->session['__bimtCharityUserRole'] == 3) {
    $actionBtn = '{view}{update}';
} else if (\Yii::$app->session['__bimtCharityUserRole'] == 4) {
    $actionBtn = '{view}';
    $allowActivate = false;
    $allowCreate = false;
}
?>
<div class="box box-primary">

    <div class="box-body">
        <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

        <p>
            <?= ($allowCreate) ? Html::a('Create Members', ['create'], ['class' => 'btn btn-success']) : "" ?>
        </p>

        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'rowOptions' => function ($model) {
                if ($model->is_active_donor == 0 && $model->is_exception == 0) {
                    return [
                        'style' => 'background-color: #fff5f5;',
                    ];
                }
                if ($model->is_active_donor == 1) {
                    return [
                        'style' => 'background-color:#f0fff4;'
                    ];
                }
                if ($model->is_exception == 1) {
                    return ['style' => 'background-color:#f3f8ff;'];
                }
                return [];
            },
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'member_code',
                [
                    'attribute' => 'fullname',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $html = Html::encode($model->fullname);

                        if ($model->is_active_donor == 1) {
                            $html .= '<br><span class="label label-success">Active Donor</span>';
                        }
                        if ($model->is_active_donor == 0 && $model->is_exception == 0) {
                            $html .= '<br><span class="label label-danger">Inactive Donor</span>';
                        }
                        if ($model->is_exception == 1) {
                            $html .= '<br><span class="label label-info">Special Member</span>';
                        }

                        return $html;
                    },
                ],
                //'image',
                'email:email',
                'phone',
                'address:ntext',
                'recurring_amount',
                'currency.code',
                [
                    'attribute' => 'invited_user_id',
                    'value' => function ($model) {
                        return !empty($model->invitedBy) ? $model->invitedBy->fullname : '';
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'invited_user_id', app\helpers\AppHelper::getAllUsers(), ['class' => 'form-control', 'prompt' => 'Filter'])
                ],
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
                            'disabled' => ($allowActivate) ? false : true,
                        ])
                        . '<label class="onoffswitch-label" for="myonoffswitch' . $model->user_id . '"></label></div>';
                    },
                    'filter' => Html::activeDropDownList($searchModel, 'is_active', [1 => 'Active', 0 => 'Inactive'], ['class' => 'form-control', 'prompt' => 'Filter']),
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => $actionBtn,
                    'buttons' => [
                        'reset' => function ($url, $model) {
                            return Html::a('<i class="glyphicon glyphicon-refresh"></i>', ['user/resend', 'id' => $model->user_id], [
                                'onclick' => 'return confirm("Are you sure you want to reset this user?")'
                            ]);
                        },
                        'push' => function ($url, $model) {
                            return Html::a(
                                    '<i class="glyphicon glyphicon-bell"></i>',
                                    'javascript:void(0)',
                                    [
                                        'title' => 'Send Push Notification',
                                        'onclick' => 'app.sendPushNotification(' . $model->user_id . '); return false;',
                                    ]
                            );
                        },
                    ],
                ],
            ],
        ]);
        ?>
    </div>

</div>