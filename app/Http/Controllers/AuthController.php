<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    # login function that give us datas for user and most important the token
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => "Fill all your fields"]);
        }

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

    # register function that insert data in database to help to regsiter users in database
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


    # respondWithToken just a method that help use to get more formated the response and get token from response
    protected function respondWithToken($token) {
        $expiresInMinutes = config('jwt.ttl') / 60;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiresInMinutes
        ]);
    }
}
