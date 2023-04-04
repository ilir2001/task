<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request){
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Create an access token for the newly registered user
        $token = $user->createToken('token-name')->accessToken;

        event(new Registered($user));

        return response()->json([
            'message' => 'User registered successfully.',
            'user' => $user,
            'access_token' => $token
        ], 201);
    }
}
