<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotEqualNotZeroReviewerEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $emailLog;
    public $reviewedByName;
    public $whoseCallReviewedName;
    public $email;
    public $submittedAmount;
    public $reviewedAmount;
    public $fromDate;
    public $toDate;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailLog, $reviewedByName,$whoseCallReviewedName, $email,$submittedAmount,$reviewedAmount, $fromDate, $toDate)
    {
        $this->emailLog = $emailLog;
        $this->reviewedByName = $reviewedByName;
        $this->whoseCallReviewedName = $whoseCallReviewedName;
        $this->email = $email;
        $this->submittedAmount = $submittedAmount;
        $this->reviewedAmount = $reviewedAmount;
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
        return $this->markdown('emails.not_eqaul_not_zero_reviewer')->subject('Phone Bill Reviewed by Administration');
    }
}