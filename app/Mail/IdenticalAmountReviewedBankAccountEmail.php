<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class IdenticalAmountReviewedBankAccountEmail extends Mailable
{
  
    use Queueable, SerializesModels;
    public $emailLog;
    public $username;
    public $email;
    public $identifiedAmount;
    public $fromDate;
    public $toDate;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailLog, $username, $email,$identifiedAmount, $fromDate, $toDate)
    {
        $this->emailLog = $emailLog;
        $this->username = $username;
        $this->email = $email;
        $this->identifiedAmount = $identifiedAmount;
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
        return $this->markdown('emails.identical_amount_reviewed_bank_account');
    }
}