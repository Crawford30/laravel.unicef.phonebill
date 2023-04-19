<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AmountGreaterThanZeroPayrollEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $emailLog;
    public $username;
    public $email;
    public $submittedAmount;
    public $reviewedAmount;
    public $fromDate;
    public $toDate;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailLog, $username, $email,$submittedAmount,$reviewedAmount, $fromDate, $toDate)
    {
        $this->emailLog = $emailLog;
        $this->username = $username;
        $this->email = $email;
        $this->submittedAmount = $submittedAmount;
        $this->reviewedAmount = $reviewedAmount;
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
        return $this->markdown('emails.amount_greater_than_zero_payroll');
    }
}