<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckPayrollEmailReminderBSC extends Mailable
{
    use Queueable, SerializesModels;

    use Queueable, SerializesModels;
    public $emailLog;
    public $email;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailLog, $email )
    {
        $this->emailLog = $emailLog;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.check_payroll_email_reminder_bsc')->subject('Reminder to Check Payroll');
    }
}