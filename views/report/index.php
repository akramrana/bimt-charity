<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
app\assets\DateRangePickerAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\NotificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Report';
$this->params['breadcrumbs'][] = $this->title;
$get = Yii::$app->request->queryParams;

$startDate = date('Y-m-d');
$endDate = date('Y-m-d', strtotime($startDate . '+1 month'));
$date_range = '';

if (!empty($get)) {
    $dateRange = explode(' to ', str_replace("/", "-", $get['date_range']));
    $monthStart = $dateRange[0];
    $monthEnd = $dateRange[1];
    $startDate = $monthStart;
    $endDate = $monthEnd;
    $date_range = $get['date_range'];
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

        <label>Date Range</label>
        <?=
        Html::textInput('date_range', $date_range, [
            'class' => 'form-control',
            'id' => 'date_range'
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
            <p><b>Reporting Date:</b> <?= date('j F,Y',strtotime($monthStart)); ?> to <?= date('j F,Y',strtotime($monthEnd)); ?></p>
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
                            Total Balance <?= $tb['code']; ?>
                        </td>
                        <td>
                            <b><?php echo ($tb['received_amount'] - $tb['expense_amount'] - $tb['donate_amount']); ?> <?= $tb['code']; ?></b>
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
<?php
$this->registerJs("$('#date_range').daterangepicker({
    autoUpdateInput: false,
    locale: {
      format: 'YYYY-MM-DD'
    },   
    timePicker: false,
    startDate:'" . $startDate . "',
    endDate:'" . $endDate . "',
});
$('input[id=\"date_range\"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
});
", \yii\web\View::POS_END, 'date-range-picker');
