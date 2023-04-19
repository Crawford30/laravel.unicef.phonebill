<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MonthlyCallLogStaffEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailLog;
    public $username;
    public $userphonenumber;
    public $fromDate;
    public $toDate;
    public $totalUniqueNumbersCalled;
    public $totalCost;
    public $identificationDeadline;
    public $email;
    public $phoneBillId;



    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailLog, $username, $userphonenumber, $fromDate,$toDate, $totalUniqueNumbersCalled,$totalCost,$identificationDeadline,$email, $phoneBillId)
    {
        $this->emailLog = $emailLog;
        $this->username = $username;
        $this->userphonenumber = $userphonenumber;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->totalUniqueNumbersCalled = $totalUniqueNumbersCalled;
        $this->totalCost = $totalCost;
        $this->identificationDeadline = $identificationDeadline;
        $this->email = $email;
        $this->phoneBillId = $phoneBillId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->markdown('emails.monthly_call_log_staff')->subject('Monthly Call Log');
        // return $this->view('view.name');
    }
}