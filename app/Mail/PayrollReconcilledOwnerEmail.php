<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PayrollReconcilledOwnerEmail extends Mailable
{
    

    use Queueable, SerializesModels;
    public $emailLog;
    public $name;
    public $email;
    public $reconcilledAmount;
    public $fromDate;
    public $toDate;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailLog, $name, $email,$reconcilledAmount, $fromDate, $toDate)
    {
        $this->emailLog = $emailLog;
        $this->name = $name;
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
        return $this->markdown('emails.payroll_reconcilled_owner')->subject('Phone Bill Reconciled from Payroll');
    }
}