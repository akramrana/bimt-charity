<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\FundRequests */

$this->title = $model->fund_request_number;
$this->params['breadcrumbs'][] = ['label' => 'Fund Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$allowUpdate = false;
$allowDelete = false;
$allowStatusChange = false;
$allowWithdrawReq = false;
if (\Yii::$app->session['__bimtCharityUserRole'] == 1) {
    $allowUpdate = true;
    $allowDelete = true;
    $allowStatusChange = true;
} else if (\Yii::$app->session['__bimtCharityUserRole'] == 2) {
    $allowUpdate = true;
    $allowDelete = true;
    $allowStatusChange = true;
} else if (\Yii::$app->session['__bimtCharityUserRole'] == 3) {
    $allowUpdate = true;
} else if (\Yii::$app->session['__bimtCharityUserRole'] == 4) {
    $fundStatus = \app\models\FundRequestStatus::find()
            ->where(['fund_request_id' => $model->fund_request_id])
            ->orderBy(['fund_request_status_id' => SORT_DESC])
            ->one();
    if (Yii::$app->user->identity->user_id == $model->request_user_id && ($fundStatus->status_id == 1 || $fundStatus->status_id == 4)) {
        $allowUpdate = true;
    }
    if (Yii::$app->user->identity->user_id == $model->request_user_id) {
        if ($fundStatus->status_id == 1 || $fundStatus->status_id == 4 || $fundStatus->status_id == 5) {
            $allowWithdrawReq = true;
        }
    }
}
$fundStatus = \app\models\FundRequestStatus::find()
        ->where(['fund_request_id' => $model->fund_request_id])
        ->orderBy(['fund_request_status_id' => SORT_DESC])
        ->one();
?>
<div class="box box-primary">

    <div class="box-body">

        <p>
            <?= ($allowUpdate) ? Html::a('Update', ['update', 'id' => $model->fund_request_id], ['class' => 'btn btn-primary']) : "" ?>
            <?=
            ($allowDelete) ? Html::a('Delete', ['delete', 'id' => $model->fund_request_id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]) : ""
            ?>
            <?=
            ($allowWithdrawReq) ? Html::a('Withdraw', ['withdraw', 'id' => $model->fund_request_id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to withdraw this request?',
                            'method' => 'post',
                        ],
                    ]) : ""
            ?>

            <?php
            if ($fundStatus->status_id === 2) {
                echo ($allowUpdate) ? Html::a('Do Release Payment', ['do-release-payment', 'id' => $model->fund_request_id], ['class' => 'btn btn-default', 'style' => 'float:right']) : "";
            }
            ?>
        </p>

        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'fund_request_number',
                [
                    'attribute' => 'request_user_id',
                    'value' => $model->requestUser->fullname
                ],
                'title',
                'request_description:ntext',
                'reason',
                'receiver_contact_details',
                'investigation_information',
                'fund_receiver_account_details',
                'additional_information',
                'request_amount',
                [
                    'attribute' => 'file',
                    'value' => !empty($model->file) ? Html::a('Download Document', \yii\helpers\BaseUrl::home() . 'uploads/' . $model->file, [
                                'download' => 'download'
                            ]) : "",
                    'format' => 'raw'
                ],
                [
                    'attribute' => 'is_active',
                    'value' => ($model->is_active == 1) ? "Active" : "Inactive"
                ],
                'currency.code',
                'created_at',
                'updated_at',
            ],
        ])
        ?>

        <p class="clearfix">
            <br/>
        </p>
        <div class="row">
            <?php
            Pjax::begin([
                'timeout' => 60000,
            ]);
            ?>
            <div class="col-md-12" id="fund-status-pjax">
                <b>Fund Status</b>
                <?php
                $dataProvider2 = new ActiveDataProvider([
                    'query' => $model->getFundRequestStatuses()->orderBy(['fund_request_status_id' => SORT_DESC]),
                    'pagination' => [
                        'pageSize' => 20,
                    ],
                ]);

                echo GridView::widget([
                    'dataProvider' => $dataProvider2,
                    'summary' => '',
                    'columns' => [
                        [
                            'label' => false,
                            'value' => function ($data) {
                                $content = "<b>" . date("M d, Y", strtotime($data->created_at)) . "</b> " . date("h:i A", strtotime($data->created_at)) . " | <b>" . $data->status->name . "</b><br/><b>" . $data->comments . "</b>";
                                return $content;
                            },
                            'format' => 'raw'
                        ]
                    ],
                ]);
                ?>
            </div>
            <?php Pjax::end(); ?>
            <div class="col-md-12">
                <?php
                if ($allowStatusChange && ($fundStatus->status_id != 3 && $fundStatus->status_id != 6 && $fundStatus->status_id != 2)) {
                    ?>
                    <div id="response"></div>
                    <b>Add Status</b>
                    <?php
                    echo Html::beginForm('', 'get', ['id' => 'fund-request-status-form']);
                    echo Html::dropDownList('status', '', \app\helpers\AppHelper::getStatusList(), ['prompt' => 'Select Status', 'class' => 'form-control select2', 'id' => 'status_id']) . '<br/>';
                    echo Html::label("Admin comments", "", [
                        'class' => 'control level',
                    ]) . '<br/>';
                    echo Html::textarea('comments', '', ['class' => 'form-control', 'style' => 'height: 100px; resize: none;', 'id' => 'comments']);
                    echo Html::hiddenInput('fund_request_id', $model->fund_request_id) . "<br/>";
                    echo Html::button('Submit', ['type' => 'button', 'class' => 'btn btn-info pull pull-right', 'onclick' => 'app.addFundStatus()']);
                    echo Html::endForm();
                }
                ?>
            </div>

        </div>

    </div>

</div>
