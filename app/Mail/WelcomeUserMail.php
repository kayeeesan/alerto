<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $first_name;
    public $username;

    public function __construct($first_name,$username)
    {
        $this->first_name = $first_name;
        $this->username = $username;
    }

    public function build()
    {
        return $this->subject('Welcome to Alerto App')
                    ->view('emails.welcome_user')
                    ->with([
                        'first_name' => $this->first_name,
                        'username' => $this->username, // Pass the username as 'email'
                    ]);
    }
}
