<?php

namespace App\Console\Commands;

use App\Config;
use App\EmailLog;
use App\Mail\UpdatePayrollStatusBSC;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class UpdatePayrollStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phonebill:updatepayrollstatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A reminder for  UNICEF BSC staff to update the status of payroll.';

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
        $configs = Config::select('payroll_day', 'bsc_email')->get();

        $daysRemaining =  (int) round((strtotime(time()))  / (60 * 60 * 24));

        foreach ($configs as $key => $config) {

            if ($daysRemaining > 1) {

                $emailLog = EmailLog::create([
                    "to" => $config->bsc_email,
                    "description" => "Reminder to Update Platform Payroll Status"
                ]);

                $emailLog->updateCode();

                try {

                    $mail = new UpdatePayrollStatusBSC($emailLog, $config->bsc_email);
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