<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EqualNotZeroReviewerEmail extends Mailable
{
   

    use Queueable, SerializesModels;
    public $emailLog;
    public $reviewedByName;
    public $whoseCallReviewedName;
    public $email;
    public $submittedAmount;
    public $fromDate;
    public $toDate;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailLog, $reviewedByName,$whoseCallReviewedName, $email,$submittedAmount, $fromDate, $toDate)
    {
        $this->emailLog = $emailLog;
        $this->reviewedByName = $reviewedByName;
        $this->whoseCallReviewedName = $whoseCallReviewedName;
        $this->email = $email;
        $this->submittedAmount = $submittedAmount;
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
        return $this->markdown('emails.eqaul_not_zero_reviewer');
    }
}