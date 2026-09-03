<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller {

    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($message = 'hello world') {
        echo $message . "\n";

        return ExitCode::OK;
    }

    public function actionSendemail() {
        $mailObject = [
            'from' => "BIMT Charity Foundation<communication@bimtcharity.org>",
            'to' => "akram.lezasolutions@gmail.com",
            'subject' => "Test Email",
            'html' => "<p>HTML test email</p>",
        ];
        \app\helpers\AppHelper::resendEmail($mailObject);
    }

    /**
     * Emails the current month's received-payment report to every active,
     * non-deleted user. The attached CSV can be opened directly in Excel.
     *
     * Usage: php yii hello/monthly-payment-report
     *
     * @return int Exit code
     */
    public function actionMonthlyPaymentReport() {
        if (date('Y-m-d') !== date('Y-m-t')) {
            return;
        }
        $monthStart = date('Y-m-01');
        $nextMonthStart = date('Y-m-01', strtotime('+1 month'));
        $monthLabel = date('F Y');

        $payments = \app\models\PaymentReceived::find()
                ->with(['donatedBy', 'receivedBy', 'currency'])
                ->where(['payment_received.is_deleted' => 0])
                ->andWhere(['>=', 'payment_received.received_date', $monthStart])
                ->andWhere(['<', 'payment_received.received_date', $nextMonthStart])
                ->orderBy(['payment_received.received_date' => SORT_ASC, 'payment_received.payment_received_id' => SORT_ASC])
                ->all();

        $users = \app\models\Users::find()
                ->where(['is_active' => 1, 'is_deleted' => 0])
                ->andWhere(['not', ['email' => null]])
                ->andWhere(['<>', 'email', ''])
                ->orderBy(['user_id' => SORT_ASC])
                ->all();

        if (empty($users)) {
            $this->stderr("No active users with an email address were found.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $csv = fopen('php://temp', 'r+');
        // UTF-8 BOM keeps names and comments readable when opened in Excel.
        fwrite($csv, "\xEF\xBB\xBF");
        fputcsv($csv, ['Invoice', 'Received Date', 'Donated By', 'Received By', 'Amount', 'Currency', 'Comments']);

        $rows = '';
        $totals = [];
        foreach ($payments as $payment) {
            $donor = $payment->donatedBy ? $payment->donatedBy->fullname : '';
            $receiver = $payment->receivedBy ? $payment->receivedBy->fullname : '';
            $currency = $payment->currency ? $payment->currency->code : '';
            $comments = (string) $payment->comments;

            fputcsv($csv, [
                $payment->received_invoice_number,
                $payment->received_date,
                $donor,
                $receiver,
                $payment->amount,
                $currency,
                $comments,
            ]);

            if (!isset($totals[$currency])) {
                $totals[$currency] = 0;
            }
            $totals[$currency] += (float) $payment->amount;

            $rows .= '<tr>'
                    . '<td>' . htmlspecialchars($payment->received_invoice_number, ENT_QUOTES, 'UTF-8') . '</td>'
                    . '<td>' . htmlspecialchars($payment->received_date, ENT_QUOTES, 'UTF-8') . '</td>'
                    . '<td>' . htmlspecialchars($donor, ENT_QUOTES, 'UTF-8') . '</td>'
                    . '<td>' . htmlspecialchars($receiver, ENT_QUOTES, 'UTF-8') . '</td>'
                    . '<td style="text-align:right">' . number_format((float) $payment->amount, 2) . '</td>'
                    . '<td>' . htmlspecialchars($currency, ENT_QUOTES, 'UTF-8') . '</td>'
                    . '<td>' . nl2br(htmlspecialchars($comments, ENT_QUOTES, 'UTF-8')) . '</td>'
                    . '</tr>';
        }

        rewind($csv);
        $csvContent = stream_get_contents($csv);
        fclose($csv);

        $totalParts = [];
        foreach ($totals as $currency => $amount) {
            $totalParts[] = number_format($amount, 2) . ($currency !== '' ? ' ' . $currency : ' (currency not set)');
        }
        $totalText = empty($totalParts) ? '0.00' : implode(', ', $totalParts);

        if ($rows === '') {
            $rows = '<tr><td colspan="7">No payments were received this month.</td></tr>';
        }

        $html = '<p>Assalamualaikum,</p>'
                . '<p>Please find the payment received report for <strong>' . $monthLabel . '</strong>. '
                . 'The Excel-compatible report is attached to this email.</p>'
                . '<p><strong>Payments:</strong> ' . count($payments) . '<br><strong>Total:</strong> ' . htmlspecialchars($totalText, ENT_QUOTES, 'UTF-8') . '</p>'
                . '<table cellpadding="6" cellspacing="0" border="1" style="border-collapse:collapse;width:100%">'
                . '<thead><tr><th>Invoice</th><th>Received Date</th><th>Donated By</th><th>Received By</th><th>Amount</th><th>Currency</th><th>Comments</th></tr></thead>'
                . '<tbody>' . $rows . '</tbody></table>'
                . '<p>M&rsquo;assalam<br>Finance Control Board<br>BIMT Charity Foundation</p>';

        $attachment = [
            'filename' => 'payment-received-' . date('Y-m') . '.csv',
            'content' => base64_encode($csvContent),
        ];
        $sent = 0;
        foreach ($users as $user) {
            $mailObject = [
                'from' => 'BIMT Charity Foundation<communication@bimtcharity.org>',
                'to' => $user->email,
                'subject' => 'Payment Received Report - ' . $monthLabel,
                'html' => '<p>Dear ' . htmlspecialchars($user->fullname, ENT_QUOTES, 'UTF-8') . ',</p>' . $html,
                'attachments' => [$attachment],
            ];
            \app\helpers\AppHelper::resendEmail($mailObject);
            $sent++;
        }

        $this->stdout('Sent the ' . $monthLabel . ' report (' . count($payments) . ' payments) to ' . $sent . " users.\n");
        return ExitCode::OK;
    }

    public function actionGenerate() {
        $users = \app\models\Users::find()
                ->where(['is_deleted' => 0, 'is_active' => 1])
                ->all();
        $proccessed = 0;
        if (!empty($users)) {
            foreach ($users as $user) {
                $model = \app\models\MonthlyInvoice::find()
                        ->where(['receiver_id' => $user->user_id, 'instalment_month' => date('F'), 'instalment_year' => date('Y')])
                        ->one();
                if (empty($model)) {
                    $model = new \app\models\MonthlyInvoice();
                    $model->created_at = date('Y-m-d H:i:s');
                    $model->updated_at = date('Y-m-d H:i:s');
                    $model->monthly_invoice_number = \app\helpers\AppHelper::getNextMonthlyInvoiceNumber();
                    $model->receiver_id = $user->user_id;
                    $model->amount = $user->recurring_amount > 0 ? $user->recurring_amount : 500;
                    $model->currency_id = $user->currency_id;
                    $model->instalment_month = date('F');
                    $model->instalment_year = date('Y');
                    $model->is_paid = 0;
                    $model->is_deleted = 0;
                    if ($model->save()) {
                        $proccessed = 1;
                        $msg = 'Invoice#' . $model->monthly_invoice_number . ' generated for ' . $model->instalment_month . ' ' . $model->instalment_year . ' against receiver ' . $model->receiver->fullname;
                        \app\helpers\AppHelper::addActivityCron("MI", $model->monthly_invoice_id, $msg);

                        $subject = "Your Sadakah for " . $model->instalment_month . " " . $model->instalment_year . '(Invoice#' . $model->monthly_invoice_number . ')';
                        $mailDetails = "<p>
                                                Assalamualaikum,<br/>
                                                Dear Brother, " . $user->fullname . "
                                        </p>
                                        <p>
                                                We hope by the mercy of almighty Allah (SW) you are doing well as well as your family members.<br/> 
                                                This is the beginning of " . $model->instalment_month . ". This is why we would like to cordially request you to contribute for 'BIMT Charity Foundation' with your SADAKAH.
                                        </p>
                                        <p>
                                                Proposed amount: " . $model->amount . " " . $model->currency->code . " (more or less amount is unquestionably acceptable)<br/>
                                                Proposed deadline: 15 " . $model->instalment_month . "," . $model->instalment_year . "
                                        </p>
                                        <p>
                                                In case you need to mention a SUBJECT during Transfer, just write FOR BCF as subject. 
                                                It is strongly recommended to inform corresponding account holder (mentioning your member ID, 
                                                if possible) after transferring the money so that we can track your transaction.
                                        </p>
                                        <p>
                                                Insha Allah we will try our level best to use your SADAKAH in right way. Verily, Allah is all knowing and all seeing.
                                        </p>

                                        <p>
                                                Bank Details:
                                        </p>
                                        <p>
                                                Bangladesh:<br/>
                                                Account Holder Name: Mahmud Arafat Bin Rafiq<br/>
                                                Account Number: 114.103.0383245<br/>
                                                Bank Name: Dutch Bangla Bank Limited
                                        </p>
                                        <p>
                                                Bikash: 008801719127039 (Mahbubur Rahman)
                                        </p>
                                        <p>
                                                Germany:<br/>
                                                Account Holder Name: MD Alif Khondokar<br/>
                                                IBAN: DE78 1007 0024 0214 2370 00<br/>
                                                BIC: DEUTDEDBBER
                                        </p>
                                        <p>
                                                Singapore:<br/>
                                                Account Holder Name: Mohin Md Rakibul Ahsun<br/>
                                                Bank Name: POSB<br/>
                                                Account Number: 248-85387-5 (Savings) 
                                        </p>
                                        <p>
                                                May Allah accept our SADAKAH, our all efforts and make these a good reason go acquire Allah's satisfaction in Dunya and in Akhira.
                                        </p>
                                        <p>
                                                M’assalam<br/>
                                                Finance Control Board<br/>
                                                BIMT Charity Foundation<br/>
                                                For detail please contact with Rafiq Bin Arafat, Rakibul Ahsun Mohin, Alif Khondokar.<br/>
                                                Web portal Link: http://bimtcharity.org/site/login
                                        </p>";

                        $mailObject = [
                            'from' => "BIMT Charity Foundation<communication@bimtcharity.org>",
                            'to' => $user->email,
                            'subject' => $subject,
                            'html' => $mailDetails,
                        ];
                        \app\helpers\AppHelper::resendEmail($mailObject);

                        $pushHelper = new \app\helpers\PushHelper();
                        $pushHelper->sendPushToUser($model->receiver_id, [
                            'title' => 'New Invoice',
                            'body' => 'Monthly invoice ' . $model->monthly_invoice_number . ' has been generated.',
                            'screen' => 'invoice',
                            'id' => $model->monthly_invoice_id,
                        ]);
                    }
                }
            }
        }
        return ExitCode::OK;
    }

    public function actionNotifyUnpaid() {
        $models = \app\models\MonthlyInvoice::find()
                ->where(['instalment_month' => date('F'), 'instalment_year' => date('Y')])
                ->andWhere(['is_paid' => 0, 'is_deleted' => 0])
                ->all();

        if (!empty($models)) {
            foreach ($models as $model) {
                $subject = "Unpaid:Sadakah for " . $model->instalment_month . " " . $model->instalment_year . '(Invoice#' . $model->monthly_invoice_number . ')';

                $mailDetails = "<p>
                                    Assalamualaikum,<br/>
                                    Dear Brother, " . $model->receiver->fullname . "
                                </p>
                                <p>
                                    We hope by the mercy of almighty Allah (SW) you are doing well as well as your family members.<br/> 
                                    This is the beginning of " . $model->instalment_month . ". This is why we would like to cordially request you to contribute for 'BIMT Charity Foundation' with your SADAKAH.
                                </p>
                                <p>
                                    Proposed amount: " . $model->amount . " " . $model->currency->code . " (more or less amount is unquestionably acceptable)<br/>
                                    Proposed deadline: 15 " . $model->instalment_month . "," . $model->instalment_year . "
                                </p>
                                <p>
                                    In case you need to mention a SUBJECT during Transfer, just write FOR BCF as subject. 
                                    It is strongly recommended to inform corresponding account holder (mentioning your member ID, 
                                    if possible) after transferring the money so that we can track your transaction.
                                </p>
                                <p>
                                    Insha Allah we will try our level best to use your SADAKAH in right way. Verily, Allah is all knowing and all seeing.
                                </p>

                                <p>
                                    Bank Details:
                                </p>
                                <p>
                                    Bangladesh:<br/>
                                    Account Holder Name: Mahmud Arafat Bin Rafiq<br/>
                                    Account Number: 114.103.0383245<br/>
                                    Bank Name: Dutch Bangla Bank Limited
                                </p>
                                <p>
                                    Bikash: 008801719127039 (Mahbubur Rahman)
                                </p>
                                <p>
                                    Germany:<br/>
                                    Account Holder Name: MD Alif Khondokar<br/>
                                    IBAN: DE78 1007 0024 0214 2370 00<br/>
                                    BIC: DEUTDEDBBER
                                </p>
                                <p>
                                    Singapore:<br/>
                                    Account Holder Name: Mohin Md Rakibul Ahsun<br/>
                                    Bank Name: POSB<br/>
                                    Account Number: 248-85387-5 (Savings) 
                                </p>
                                <p>
                                    May Allah accept our SADAKAH, our all efforts and make these a good reason go acquire Allah's satisfaction in Dunya and in Akhira.
                                </p>
                                <p>
                                    M’assalam<br/>
                                    Finance Control Board<br/>
                                    BIMT Charity Foundation<br/>
                                    For detail please contact with Rafiq Bin Arafat, Rakibul Ahsun Mohin, Alif Khondokar.<br/>
                                    Web portal Link: http://bimtcharity.org/site/login
                                </p>";

                $mailObject = [
                    'from' => "BIMT Charity Foundation<communication@bimtcharity.org>",
                    'to' => $model->receiver->email,
                    'subject' => $subject,
                    'html' => $mailDetails,
                ];
                \app\helpers\AppHelper::resendEmail($mailObject);

                $pushHelper = new \app\helpers\PushHelper();
                $pushHelper->sendPushToUser($model->receiver_id, [
                    'title' => 'Unpaid Invoice',
                    'body' => 'Monthly invoice ' . $model->monthly_invoice_number . ' has been generated. Please complete the payment.',
                    'screen' => 'invoice',
                    'id' => $model->monthly_invoice_id,
                ]);
            }
        }
    }
}
