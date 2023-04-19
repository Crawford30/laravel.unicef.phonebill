<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class IdentificationCompletedStaffEmail extends Mailable
{
    use Queueable, SerializesModels;


    public $emailLog;
    public $username;
    public $fromDate;
    public $toDate;
    public $email;
    public $officialCalls;
    public $personalCalls;
    public $amountOwed;
    public $paymentMethod;
    public $officialCallsPercentage;
    public $personalCallsPercentage;
    public $allowanceAmount;
    



   
    

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailLog, $username,  $fromDate,$toDate, $email,$officialCalls,$personalCalls,$amountOwed, $paymentMethod, $officialCallsPercentage, $personalCallsPercentage, $allowanceAmount)
    {
        $this->emailLog = $emailLog;
        $this->username = $username;
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
        $this->allowanceAmount = $allowanceAmount;
    }
    
    /**
     * Build the message.
     *
     * @return $this  identification_completed_staff
     */
    public function build()
    {
        return $this->markdown('emails.identification_completed_staff')->subject('Call Log Identification Completed');
    }
}