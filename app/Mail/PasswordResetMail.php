<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PasswordResetMail extends Mailable
{
    public $token;
    public $username;

    public function __construct($token, $username)
    {
        $this->token = $token;
        $this->username = $username;
    }

    public function build()
    {
        $url = url("/update-password?token={$this->token}&username={$this->username}");
        
        return $this->view('emails.password_reset')
            ->subject('Password Reset Request')
            ->with(['url' => $url]);
    }
}
