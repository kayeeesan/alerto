<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Resources\User as ResourcesUser;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = \App\Models\User::whereRaw('LOWER(username) = ?', [strtolower($request->username)])->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Incorrect credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($user->status === 'pending') {
            return response([
                'message' => 'Account is pending'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($user->status === 'disabled') {
            return response([
                'message' => 'Account is disabled'
            ], Response::HTTP_UNAUTHORIZED);
        }

        Auth::login($user); 

        return response([
            'user' => new ResourcesUser($user),
            'access_token' => $user->createToken('MyApp')->plainTextToken
        ], Response::HTTP_OK);
    }


    public function logout()
    {
        Auth::guard('web')->logout();

        return response([
            'message' => 'Successfully logout.'
        ], Response::HTTP_OK);
    }

    public function setPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = Auth::user();
            $user->password = Hash::make($request->password);
            $user->password_reset = false;
            $user->update();

            return response()->json(['message' => 'Password successfully saved.']);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}