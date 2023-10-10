<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenants\Auth\LoginRequest;

use Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $request->authenticate();

        $user = $request->user();

        $accessToken = $user->createToken('personal-access-token')->accessToken;

        return response()->json([
            'user' => $user,
            'access_token' => $accessToken
        ], 201);
    }

    public function logout()
    {
        $accessToken = Auth::user()->token();

        $accessToken->revoke();

        return response()->noContent();
    }
}
