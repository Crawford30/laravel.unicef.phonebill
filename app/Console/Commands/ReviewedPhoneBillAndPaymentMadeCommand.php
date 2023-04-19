<?php

namespace App\Console\Commands;

use App\User;
use DateTime;
use App\Config;
use App\EmailLog;
use Carbon\Carbon;
use App\CallLogTimeline;
use App\CallLogPayment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmReceitOfFundsEmailReminderBSC;

class ReviewedPhoneBillAndPaymentMadeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phonebill:reviewed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A UNICEF Staff having Phone bill that has been reviewed and passed onto BSC and he/she has uploaded payment receipt and selected payment type as UNICEF Bank Account';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $configs = Config::select('bsc_email')->get();

        $daysAfterPayment = [2, 4, 8];
        $callLogDetails = CallLogPayment::where('bill_type', 'UNICEF BANK ACCOUNT')->where('status', 'Reviewed')->whereNotNull('document_one')->where('invoice_status', 'Settled')->get();
        foreach ($callLogDetails as $key => $callLogDetail) {

            $callLogTimeLines = CallLogTimeline::where('call_log_payment_id', $callLogDetail->id)->get();

            $users = User::where('id', $callLogDetail->bill_owner_id)->whereNotNull('email')->get();

            foreach ($callLogTimeLines as $key => $callLogTimeLine) {

                $daysAfter = (int) round((time() - strtotime($callLogTimeLine->updated_at))  / (60 * 60 * 24));

                foreach ($daysAfterPayment as $dayAfterPayement) {

                    if ($daysAfter > $dayAfterPayement) {

                        foreach ($configs as $key => $config) {

                            foreach ($users as $user) {

                                $emailLog = EmailLog::create([
                                    "to" => $config->bsc_email,
                                    "description" => "Check and Confirm Receipt of Payment"
                                ]);

                                $emailLog->updateCode();

                                $date = new DateTime($callLogTimeLine->updated_at);
                                $newDate = ($date->format('d/M/Y'));

                                try {
                                    $mail = new ConfirmReceitOfFundsEmailReminderBSC($emailLog, $user->name, date('d/M/Y', strtotime($callLogDetail->from_date)), date('d/M/Y', strtotime($callLogDetail->to_date)), $config->bsc_email,  $newDate,  $callLogDetail->reviewed_amount);
                                    $emailLog->update([
                                        "body" => $mail->render()
                                    ]);
                                    Mail::to($user->email)->send($mail);
                                } catch (\Exception $exception) {
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}