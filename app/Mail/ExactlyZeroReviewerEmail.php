<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExactlyZeroReviewerEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailLog;
    public $reviewedByName;
    public $whoseCallReviewedName;
    public $email;
    public $fromDate;
    public $toDate;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailLog,  $reviewedByName,$whoseCallReviewedName, $email, $fromDate, $toDate)
    {
        $this->emailLog = $emailLog;
        $this->reviewedByName = $reviewedByName;
        $this->whoseCallReviewedName = $whoseCallReviewedName;
        $this->email = $email;
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
        return $this->markdown('emails.exactly_zero_reviewer')->subject('Phone Bill Reviewed by Administration');
    }
}