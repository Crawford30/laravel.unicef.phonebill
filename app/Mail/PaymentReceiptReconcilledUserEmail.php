<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentReceiptReconcilledUserEmail extends Mailable
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
        return $this->markdown('emails.payment_receipt_reconcilled_user')->subject('Phone Bill Payment Settled');
    }
}