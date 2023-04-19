<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AmountGreaterThanZeroSelectedPayrollBSCEmail extends Mailable
{
    use Queueable, SerializesModels;

   
    public $emailLog;
    public $fromDate;
    public $toDate;
    public $fromName;
    public $reviewedAmount;
    public $identificationDeadline;
    public $email;
    public $phoneBillId;



    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailLog, $fromDate,$toDate, $fromName,$reviewedAmount,$email, $phoneBillId)
    {
        $this->emailLog = $emailLog;

        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->fromName = $fromName;
        $this->reviewedAmount = $reviewedAmount;
        $this->email = $email;
        $this->phoneBillId = $phoneBillId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.amount_greater_than_zero_selected_payroll_bsc');
    }
}