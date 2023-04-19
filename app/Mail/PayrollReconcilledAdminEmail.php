<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PayrollReconcilledAdminEmail extends Mailable
{
  
    use Queueable, SerializesModels;
    public $emailLog;
    public $forName;
    public $toName;
    public $email;
    public $reconcilledAmount;
    public $fromDate;
    public $toDate;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailLog, $forName,$toName, $email,$reconcilledAmount, $fromDate, $toDate)
    {
        $this->emailLog = $emailLog;
        $this->forName = $forName;
        $this->toName = $toName;
        $this->email = $email;
        $this->reconcilledAmount = $reconcilledAmount;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;

    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.payroll_reconcilled_admin')->subject('Phone Bill Reconciled');;
    }
}