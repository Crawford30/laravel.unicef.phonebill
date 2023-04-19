<?php

namespace App\Console\Commands;

use App\Config;
use App\EmailLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\CheckPayrollEmailReminderBSC;

class CheckPayrollReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phonebill:checkpayroll';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminder to BSC when its 2 days prior to payroll.';

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
        $configs = Config::select('payroll_day', 'bsc_email','reminder_days')->whereNotNull('payroll_day')->whereNotNull('bsc_email')->whereNotNull('reminder_days')->get();
        $daysRemaining =  (int) round((strtotime(time()))  / (60 * 60 * 24));

        $time = Carbon::now()->format('H:i:m');
       

        // dd( (int)round((strtotime(time()))  / (60 * 60 * 24)));

        foreach ($configs as $key => $config) {

           // dd(($config->payroll_day - $daysRemaining) === 2);

            if (($config->payroll_day - $daysRemaining) === 2) {

                $emailLog = EmailLog::create([
                    "to" => $config->bsc_email,
                    "description" => "Reminder to Check Payroll"
                ]);

                $emailLog->updateCode();

                try {

                    $mail = new CheckPayrollEmailReminderBSC($emailLog, $config->bsc_email);
                    $emailLog->update([
                        "body" => $mail->render()
                    ]);
                    Mail::to($config->bsc_email)->send($mail);
                } catch (\Exception $exception) {
                }
            }
        }
    }
}