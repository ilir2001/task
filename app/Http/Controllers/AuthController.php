<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class AuthController extends Controller
{
    /**
     * Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (! $user || ! Hash::check($request->password, $user->password)) {
           return ['status'=>0,'message'=>'Invalid Credentials','data'=>[]];
        }
        $data = [
            'user'=>$user,
            'token'=>$user->createToken('MyToken')->accessToken
        ];
        return ['status'=>1, 'message'=>'Login Successful!', 'data'=>$data];
    }   

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => "Fill all your fields"]);
        }
 
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
       
        $token = $user->createToken('PassportAuth')->accessToken;
 
        return response()->json(['token' => $token], 200);
    }


    
    protected function respondWithToken($token) {
    $expiresInMinutes = config('jwt.ttl') / 60; // Get token TTL from config

    return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => $expiresInMinutes
        ]);
    }
}
