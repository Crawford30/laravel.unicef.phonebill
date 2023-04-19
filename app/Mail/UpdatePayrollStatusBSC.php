<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdatePayrollStatusBSC extends Mailable
{
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
        return $this->markdown('emails.update_payroll_status_bsc')->subject('Reminder to Update Platform Payroll Status');
    }
}