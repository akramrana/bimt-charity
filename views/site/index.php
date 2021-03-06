<?php
/* @var $this yii\web\View */

$this->title = 'Dashboard';
?>
<div class="row">
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-yellow"><ion-icon name="people"></ion-icon></span>

            <div class="info-box-content">
                <span class="info-box-text">Number of Members</span>
                <span class="info-box-number"><?= $users; ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-green"><ion-icon name="mail"></ion-icon></span>

            <div class="info-box-content">
                <span class="info-box-text">Invoice Sent This Month</span>
                <span class="info-box-number"><?= $monthlyInvoice['invoice_count']; ?></span>

                <span class="info-box-more"><?php
                    foreach ($monthlyInvoiceCurrencyWise as $miw) {
                        ?>
                        <?= number_format($miw['amount'], 2) . ' ' . $miw['code']; ?> || 
                        <?php
                    }
                    ?>
                </span>

            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><ion-icon name="cash"></ion-icon></span>

            <div class="info-box-content">
                <span class="info-box-text">Payment Receive This Month</span>
                <span class="info-box-number"><?= $payment_received['receive_count']; ?></span>
                <span class="info-box-more">
                    <?php
                    foreach ($paymentReceivedCurrencyWise as $prw) {
                        ?>
                        <?= number_format($prw['amount'], 2) . ' ' . $prw['code']; ?> || 
                        <?php
                    }
                    ?>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-red"><ion-icon name="pricetags"></ion-icon></span>

            <div class="info-box-content">
                <span class="info-box-text">Fund Request This Month</span>
                <span class="info-box-number"><?= $fund_request['fund_request_count']; ?></span>
                <span class="info-box-more">
                    <?php
                    foreach ($fundRequestCurrencyWise as $fcw) {
                        ?>
                        <?= number_format($fcw['amount'], 2) . ' ' . $fcw['code']; ?>
                        <?php
                    }
                    ?>
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-green"><ion-icon name="card"></ion-icon></span>

            <div class="info-box-content">
                <span class="info-box-text">Payment Release This Month</span>
                <span class="info-box-number"><?= $payment_release['release_count']; ?></span>
                <span class="info-box-more">
                    <?php
                    foreach ($paymentReleaseCurrencyWise as $prc) {
                        ?>
                        <?= number_format($prc['amount'], 2) . ' ' . $prc['code']; ?> || 
                        <?php
                    }
                    ?>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-aqua-gradient"><ion-icon name="pricetags"></ion-icon></span>

            <div class="info-box-content">
                <span class="info-box-text">Expense This Month</span>
                <span class="info-box-number"><?= $expenses['expense_count']; ?></span>
                <span class="info-box-more">
                    <?php
                    foreach ($expensesCurrencyWise as $ecw) {
                        ?>
                        <?= number_format($ecw['amount'], 2) . ' ' . $ecw['code']; ?> || 
                        <?php
                    }
                    ?>
                </span>
            </div>
        </div>
    </div>

</div>
<div class="row">
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Fund Request - By Status</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="box-body">
                <?php
                if (!empty($stats)) {
                    foreach ($stats as $key => $stat) {
                        ?>
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-purple"><ion-icon name="list-box"></ion-icon></span>

                                <div class="info-box-content">
                                    <span class="info-box-text"><?= $stat['name']; ?></span>
                                    <span class="info-box-number"><?= $stat['fund_request_count']; ?></span>
                                    <span class="info-box-more">
                                        <?php
                                        foreach ($stat['fund_stat_curr_wise'] as $fscw) {
                                            ?>
                                            <?= number_format($fscw['amount'], 2) . ' ' . $fscw['code']; ?> || 
                                            <?php
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>

</div>
<div class="row">
    <div class="col-md-4">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Login History(last 10)</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Datetime</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($login_history)) {
                                foreach ($login_history as $lh) {
                                    $dateTime = new \DateTime($lh->datetime);
                                    $dateTime->setTimezone(new \DateTimeZone('Asia/Dhaka'));
                                    ?>
                                    <tr>
                                        <td><?= $lh->user->fullname; ?></td>
                                        <td><?= $dateTime->format('F j Y h:i A'); ?> (BST)</td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Activity Log(last 10)</h3>

                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <th>Log Message</th>
                                <th>Datetime</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($activity_log)) {
                                foreach ($activity_log as $al) {
                                    $dateTime1 = new \DateTime($al->created_at);
                                    $dateTime1->setTimezone(new \DateTimeZone('Asia/Dhaka'));
                                    ?>
                                    <tr>
                                        <td><?= $al->comments; ?></td>
                                        <td><?= $dateTime1->format('F j Y h:i A'); ?> (BST)</td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
        </div>
    </div>
</div>