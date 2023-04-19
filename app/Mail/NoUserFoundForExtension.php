<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NoUserFoundForExtension extends Mailable
{
    use Queueable, SerializesModels;


    public $emailLog;
    public $email;
    public $username;
    public $name;
    public $fromDate;
    public $toDate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailLog, $email, $username, $name, $fromDate, $toDate)
    {
        $this->emailLog = $emailLog;
        $this->email = $email;
        $this->username = $username;
        $this->name = $name;
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
        return $this->markdown('emails.no_user_found_for_extension')->subject('No User Found For The Extensions');
    }
}
