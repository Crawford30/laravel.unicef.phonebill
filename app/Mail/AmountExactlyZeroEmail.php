<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AmountExactlyZeroEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailLog;
    public $username;
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
    public function __construct($emailLog, $username,  $reviewedByName,$whoseCallReviewedName, $email, $fromDate, $toDate)
    {
        $this->emailLog = $emailLog;
        $this->username = $username;
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
        return $this->markdown('emails.amount_exactly_zero')->subject('Phone Bill Reviewed by Administration');
    }
}