<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailReminderPaymentConfirmationEmail extends Mailable
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
    public function __construct($emailLog, $username, $email, $identifiedAmount, $fromDate, $toDate)
    {
        $this->emailLog = $emailLog;
        $this->username = $username;
        $this->email = $email;
        $this->identifiedAmount = $identifiedAmount;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function build()
    {
        return $this->markdown('emails.email_reminder_payment_confirmation')->subject('Upload Phone Bill Payment Confirmation');
    }
}