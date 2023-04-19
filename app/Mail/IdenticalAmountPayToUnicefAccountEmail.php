<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class IdenticalAmountPayToUnicefAccountEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $emailLog;
    public $username;
    public $email;
    public $submittedAmount;
    public $fromDate;
    public $toDate;
    public $phoneBillId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($phoneBillId,$emailLog, $username, $email,$submittedAmount, $fromDate, $toDate)
    {
        $this->emailLog = $emailLog;
        $this->username = $username;
        $this->email = $email;
        $this->submittedAmount = $submittedAmount;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->phoneBillId = $phoneBillId;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.identical_amount_pay_to_unicef_account');
    }
}