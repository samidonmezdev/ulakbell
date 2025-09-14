<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return ApiResponse::unauthorized('Geçersiz kimlik bilgileri');
        }

        /** @var User $user */
        $user = User::where('email', $credentials['email'])->first();
        $token = $user->createToken('api')->plainTextToken;

        return ApiResponse::success([
            'token' => $token,
            'user' => $user,
        ], 'Giriş başarılı');
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
        return ApiResponse::success(null, 'Çıkış yapıldı');
    }

    public function me(Request $request)
    {
        return ApiResponse::success($request->user());
    }
} 