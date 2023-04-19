<?php

namespace App\Console\Commands;

use App\User;
use DateTime;
use App\EmailLog;
use App\CallLogTimeline;
use App\CallLogPayment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailReminderPaymentConfirmationEmail;

class UploadPhoneBillConfirmationReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phonebill:uploadpaymentreceipt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a scheduled reminder to a UNICEF Staff who hasnt uploaded payment confirmation for his/her phone bills.';

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

        $daysAfterReview = [2, 4, 8];
        $callLogDetails = CallLogPayment::where('bill_type', 'UNICEF BANK ACCOUNT')->where('status', 'Reviewed')->where('document_one', null)->get();

        foreach ($callLogDetails as $key => $callLogDetail) {
            $callLogTimeLines = CallLogTimeline::select('date_reviewed', 'identified_by_id')->where('call_log_payment_id', $callLogDetail->id)->whereNotNull('date_reviewed')->get();
            foreach ($callLogTimeLines as $key => $callLogTimeLine) {

                $dateString = str_replace('/', '-', $callLogTimeLine->date_reviewed);
                $date = new DateTime($dateString);
                $newDate = ($date->format('d-m-Y'));
                $idenified_by_id = $callLogTimeLine->identified_by_id;
                $users = User::where('id', $idenified_by_id)->whereNotNull('email')->get();

                $daysRemaining =  (int) round((time() - strtotime($newDate))  / (60 * 60 * 24));
                foreach ($daysAfterReview as $dayAfterReview) {

                    if ($dayAfterReview === $daysRemaining) {

                        foreach ($users as $user) {
                            $emailLog = EmailLog::create([
                                "to" => $user->email,
                                "description" => "Upload Phone Bill Payment Confirmation"
                            ]);

                            $emailLog->updateCode();
                            try { 
                                $mail = new EmailReminderPaymentConfirmationEmail($emailLog, $user->name, $user->email, $callLogDetail->reviewed_amount, date('d/M/Y', strtotime($callLogDetail->from_date)),  date('d/M/Y', strtotime($callLogDetail->to_date)));
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