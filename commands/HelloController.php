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
                    if ($user->recurring_amount > 0) {
                        $model = new \app\models\MonthlyInvoice();
                        $model->created_at = date('Y-m-d H:i:s');
                        $model->updated_at = date('Y-m-d H:i:s');
                        $model->monthly_invoice_number = \app\helpers\AppHelper::getNextMonthlyInvoiceNumber();
                        $model->receiver_id = $user->user_id;
                        $model->amount = $user->recurring_amount;
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
                        } else {
                            die(json_encode($model->errors));
                        }
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
