<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NotificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Report';
$this->params['breadcrumbs'][] = $this->title;
$get = Yii::$app->request->queryParams;
$month = null;
$year = null;
if (!empty($get)) {
    $month = $get['instalment_month'];
    $year = $get['instalment_year'];
}
?>
<div class="box box-primary">

    <div class="box-body">

        <?php
        $form = ActiveForm::begin([
                    'action' => ['index'],
                    'method' => 'get',
        ]);
        ?>

        <label>Month</label>
        <?=
        Html::dropDownList('instalment_month', $month, app\helpers\AppHelper::monthList(), [
            'class' => 'form-control'
        ])
        ?>

        <label>Year</label>
        <?=
        Html::dropDownList('instalment_year', $year, app\helpers\AppHelper::YearsList(), [
            'class' => 'form-control'
        ])
        ?>

        <span class="clearfix">&nbsp;</span>
        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <?php
        if (!empty($get)) {
            ?>
            <hr/>
            <p><b>Reporting Month:</b> <?= $get['instalment_month']; ?> <?= $get['instalment_year']; ?></p>
            <p><b>Total Members:</b> <?= $totalMembers; ?> member(s)</p>
            <p><b>Contributed:</b> <?= $paymentReceived; ?> member(s)</p>
            <p><b>Did not Contributed:</b> <?= ($totalMembers - $paymentReceived); ?> member(s)</p>
            <hr/>
            <h4>Incoming Payment Overview</h4>
            <table class="table table-bordered table-striped">
                <tr>
                    <th>
                        Description
                    </th>
                    <th>
                        Amount
                    </th>
                </tr>
                <?php
                foreach ($incoming as $ic) {
                    ?>
                    <tr>
                        <td>
                            Incoming <?= $ic['code']; ?>
                        </td>
                        <td>
                            <b><?= $ic['amount']; ?> <?= $ic['code']; ?></b>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <hr/>
            <h4>Expense Overview</h4>
            <table class="table table-bordered table-striped">
                <tr>
                    <th>
                        Description
                    </th>
                    <th>
                        Amount
                    </th>
                </tr>
                <?php
                foreach ($expenses as $ex) {
                    ?>
                    <tr>
                        <td>
                            Expense Amount <?= $ex['code']; ?> <sup>Outside of donation</sup>
                        </td>
                        <td>
                            <b><?= $ex['amount']; ?> <?= $ex['code']; ?></b>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <hr/>
            <h4>Outgoing Payment Overview</h4>
            <table class="table table-bordered table-striped">
                <tr>
                    <th>
                        Description
                    </th>
                    <th>
                        Amount
                    </th>
                </tr>
                <?php
                foreach ($outgoing as $og) {
                    ?>
                    <tr>
                        <td>
                            Outgoing <?= $og['code']; ?>
                        </td>
                        <td>
                            <b><?= $og['amount']; ?> <?= $og['code']; ?></b>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            
            <hr/>
            <h4>Total Balance Current Month</h4>
            <table class="table table-bordered table-striped">
                <tr>
                    <th>
                        Description
                    </th>
                    <th>
                        Amount
                    </th>
                </tr>
                <?php
                foreach ($totalBalances as $tb) {
                    ?>
                    <tr>
                        <td>
                            Total Balance <?=$tb['code'];?>
                        </td>
                        <td>
                            <b><?php echo ($tb['received_amount'] - $tb['expense_amount'] - $tb['donate_amount']);?> <?=$tb['code'];?></b>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <hr/>
            <h4>Member Increment Overview</h4>
            <table class="table table-bordered table-striped">
                <tr>
                    <th>
                        Name
                    </th>
                    <th>
                        Phone
                    </th>
                    <th>
                        Joined Date
                    </th>
                </tr>
                <?php
                if (!empty($users)) {
                    foreach ($users as $user) {
                        ?>
                        <tr>
                            <td>
                                <?= $user['fullname'] ?>
                            </td>
                            <td>
                                <?= $user['phone'] ?>
                            </td>
                            <td>
                                <?= date('d.m.Y', strtotime($user['created_at'])); ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </table>
            <?php
        }
        ?>
    </div>

</div>