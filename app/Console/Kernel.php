<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\PhoneBillIdentificationReminderCommand::class,
        \App\Console\Commands\CheckPayrollReminderCommand::class,
        \App\Console\Commands\ReviewedPhoneBillAndPaymentMadeCommand::class,
        \App\Console\Commands\UpdatePayrollStatusCommand::class,
        \App\Console\Commands\UploadPhoneBillConfirmationReminder::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

    //     $schedule->command('phonebill:staff')
    //     ->everyMinute();

    // $schedule->command('phonebill:uploadpaymentreceipt')
    //     ->everyMinute();

    // $schedule->command('phonebill:checkpayroll')
    //     ->everyMinute();
    // $schedule->command('phonebill:reviewed')
    //     ->everyMinute();

    // $schedule->command('phonebill:updatepayrollstatus')
    //     ->everyMinute();

        $schedule->command('phonebill:staff')
            ->daily();

        $schedule->command('phonebill:uploadpaymentreceipt')
            ->daily();

        $schedule->command('phonebill:checkpayroll')
            ->daily();
        $schedule->command('phonebill:reviewed')
            ->daily();

        $schedule->command('phonebill:updatepayrollstatus')
            ->daily();
    }



    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}