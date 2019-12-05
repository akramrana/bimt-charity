<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NotificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Activity Log';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box box-primary">

    <div class="box-body">

        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'comments:ntext',
                [
                    'attribute' => 'created_at',
                    'value' => function($model) {
                        $dateTime = new \DateTime($model->created_at);
                        $dateTime->setTimezone(new \DateTimeZone('Asia/Dhaka'));
                        return $dateTime->format('Y-m-d h:i A').' (BST)';
                    }
                ],
            //'is_deleted',
            //['class' => 'yii\grid\ActionColumn'],
            ],
        ]);
        ?>
    </div>

</div>