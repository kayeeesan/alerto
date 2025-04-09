<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\WelcomeUserMail;
use Illuminate\Support\Facades\Mail;

class MailTestController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'email' => 'required|email',
        ]);

        $username = $request->username;
        $email = $request->email;

        Mail::to($email)->send(new WelcomeUserMail($username));

        return response()->json(['message' => 'Email sent successfully to ' . $email]);
    }
}
