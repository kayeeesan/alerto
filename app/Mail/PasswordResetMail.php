<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PasswordResetMail extends Mailable
{
    public $password;
    public $username;

    public function __construct($password, $username)
    {
        $this->password = $password;
        $this->username = $username;
    }

    public function build()
    {
        // Generate the login URL
        $loginUrl = url('/login');  // Adjust this if your login route is different

        return $this->view('emails.password_reset')
            ->subject('Password Reset Request')
            ->with([
                'password' => $this->password,
                'username' => $this->username,
                'loginUrl' => $loginUrl,  // Add login URL to the email
            ]);
    }
}

