<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConfirmReceitOfFundsEmailReminderBSC extends Mailable
{
    use Queueable, SerializesModels;
    public $emailLog;
    public $fromName;
    public $fromDate;
    public $toDate;
    public $email;
    public $dateUploadedPayment;
    public $amountOwed;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailLog, $fromName,  $fromDate, $toDate, $email, $dateUploadedPayment, $amountOwed)
    {
        $this->emailLog = $emailLog;
        $this->fromName = $fromName;
        $this->fromDate = $fromDate;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->email = $email;
        $this->amountOwed = $amountOwed;
        $this->dateUploadedPayment = $dateUploadedPayment;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.confirm_receipt_of_funds_reminder_bsc')->subject('Check and Confirm Receipt of Payment');
    }
}