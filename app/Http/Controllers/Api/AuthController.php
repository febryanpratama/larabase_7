<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Utils\ResponseCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return ResponseCode::badRequest('Email atau password salah.');
        }

        $user = Auth::user();

        // buat token passport
        $token = $user->createToken('API Token')->accessToken;

        return ResponseCode::successPost([
            'user'  => $user,
            'token' => $token
        ], 'Success Login');
    }


    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Logout success'
        ]);
    }
}
