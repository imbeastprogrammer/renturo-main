<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenants\Auth\LoginRequest;
use App\Mail\Tenants\Auth\SendMobileVerificationCode;
use Carbon\Carbon;
use Mail;
use Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $verificationCode = rand(1000, 9999);

        $request->authenticate();

        $user = $request->user();

        $accessToken = $user->createToken('personal-access-token')->accessToken;

        $user->mobileVerification()->create([
            'mobile_number' => $user->mobileVerification()->mobile_number,
            'code' => $verificationCode,
            'expires_at' => Carbon::now()->addSeconds(300),
        ]);

        Mail::to($user->email)->send(new SendMobileVerificationCode(['code' => $verificationCode]));

        return response()->json([
            'user' => $user,
            'verification_code' => $verificationCode, // return temporary in response
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
