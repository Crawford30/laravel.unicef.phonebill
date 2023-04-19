<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UnicefEmailReminderToIdentifyCallsEmail extends Mailable
{
    use Queueable, SerializesModels;

    

    public $emailLog;
    public $username;
    // public $userphonenumber;
    public $fromDate;
    public $toDate;
    public $totalUniqueNumbersCalled;
    public $totalCost;
    public $identificationDeadline;
    public $email;
    public $phoneBillId;
    public $remainingDays;



    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailLog, $username, $fromDate,$toDate, $totalUniqueNumbersCalled,$totalCost,$identificationDeadline,$email, $phoneBillId, $remainingDays)
    {
        $this->emailLog = $emailLog;
        $this->username = $username;
        // $this->userphonenumber = $userphonenumber;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->totalUniqueNumbersCalled = $totalUniqueNumbersCalled;
        $this->totalCost = $totalCost;
        $this->identificationDeadline = $identificationDeadline;
        $this->email = $email;
        $this->phoneBillId = $phoneBillId;
        $this->remainingDays = $remainingDays;


        
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.unicef_staff_email_reminder_to_identify_calls')->subject('Approaching Phone Bill Identification Deadline');
    }
}