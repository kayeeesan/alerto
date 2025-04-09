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
        
        // Create a unique token
        $token = Str::random(60);

        // Store the token in the password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['username' => $username],
            ['token' => $token, 'created_at' => now()]
        );

        // Send reset link email to the user
        $user = User::where('username', $username)->first();
        Mail::to($user->username)->send(new PasswordResetMail($token, $username));

        return response()->json(['message' => 'Reset link sent successfully.']);
    }

    public function resetPassword(Request $request)
    {
        // Validate the input
        $request->validate([
            'username' => 'required|exists:users,username',
            'token' => 'required',
            'password' => 'required|confirmed',
        ]);

        // Check if the token is valid
        $tokenData = DB::table('password_reset_tokens')
            ->where('username', $request->username)
            ->where('token', $request->token)
            ->first();

        if (!$tokenData) {
            return response()->json(['message' => 'Invalid or expired token.'], 400);
        }

        // Reset the user's password
        $user = User::where('username', $request->username)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the token from the table after the password is reset
        DB::table('password_reset_tokens')->where('username', $request->username)->delete();

        return response()->json(['message' => 'Password reset successfully.']);
    }
}
