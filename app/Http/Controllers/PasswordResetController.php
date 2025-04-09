<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;

class PasswordResetController extends Controller
{
    public function sendResetLink(Request $request)
    {
        // Validate the username
        $request->validate([
            'username' => 'required|exists:users,username', // Make sure the username exists in the users table
        ]);
        
        $username = $request->input('username');
        
        // Generate a random password
        $random_password = Str::random(8); // Generates an 8-character random password
        
        // Find the user
        $user = User::where('username', $username)->first();
        
        // Set the password to the random password
        $user->password = bcrypt($random_password);
        $user->password_reset = true; // Set the password_reset flag to true
        $user->save();
        
        // Send email with the random password
        Mail::to($user->username)->send(new PasswordResetMail($random_password, $username));
        
        return response()->json(['message' => 'Password reset details sent successfully.']);
    }
    
    
}
