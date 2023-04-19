<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentReceiptReconcilledBSCEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $emailLog;
    public $forName;
    public $email;
    public $reconcilledAmount;
    public $fromDate;
    public $toDate;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailLog, $forName, $email,$reconcilledAmount, $fromDate, $toDate)
    {
        $this->emailLog = $emailLog;
        $this->forName = $forName;
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
        return $this->markdown('emails.payment_receipt_reconcilled_bsc')->subject('Phone Bill Payment Settled');
    }
}