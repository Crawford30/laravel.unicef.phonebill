<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MonthlyCallLogICTEmail extends Mailable
{
    use Queueable, SerializesModels;


    public $emailLog;
    public $username;
    public $fromDate;
    public $toDate;
    public $totalUniqueMobileNumbersCalled;
    public $totalUniquePhones;
    public $totalRecords;
    public $email;
   


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailLog, $username,  $fromDate,$toDate, $totalUniqueMobileNumbersCalled,$totalUniquePhones,$totalRecords,$email)
    {
        $this->emailLog = $emailLog;
        $this->username = $username;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->totalUniqueMobileNumbersCalled = $totalUniqueMobileNumbersCalled;
        $this->totalUniquePhones = $totalUniquePhones;
        $this->totalRecords = $totalRecords;
        $this->email = $email;
       
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
    
        return $this->markdown('emails.monthly_call_log_ict')->subject('Monthly Call Log');
    }
}