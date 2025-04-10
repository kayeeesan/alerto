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
            'first_name' => 'required|string',
            'username' => 'required|email',
        ]);

        $first_name = $request->first_name;
        $username = $request->username; 

        Mail::to($username)->send(new WelcomeUserMail($first_name, $username));

        return response()->json(['message' => 'Email sent successfully to ' . $username]);
    }
}
