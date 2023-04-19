<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class IdentificationCompletedAdministrationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailLog;
    public $fromName;
    public $fromDate;
    public $toDate;
    public $email;
    public $officialCalls;
    public $personalCalls;
    public $amountOwed;
    public $paymentMethod;
    public $officialCallsPercentage;
    public $personalCallsPercentage;
    public $toName;
    public $phoneBillId;
    public $allowanceAmount;
    


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailLog, $fromName,  $fromDate,$toDate, $email,$officialCalls,$personalCalls,$amountOwed, $paymentMethod, $officialCallsPercentage, $personalCallsPercentage, $toName, $allowanceAmount, $phoneBillId)
    {
        $this->emailLog = $emailLog;
        $this->fromName = $fromName;
        $this->fromDate = $fromDate;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->email = $email;
        $this->officialCalls = $officialCalls;
        $this->personalCalls = $personalCalls;
        $this->amountOwed = $amountOwed;
        $this->paymentMethod = $paymentMethod;
        $this->officialCallsPercentage = $officialCallsPercentage;
        $this->personalCallsPercentage = $personalCallsPercentage;
        $this->toName = $toName;
        $this->allowanceAmount = $allowanceAmount;
        $this->phoneBillId = $phoneBillId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.identification_completed_administration')->subject('Call Log Identification Completed');
    }
}