<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenants\Auth\LoginRequest;
use App\Mail\Tenants\Auth\SendMobileVerificationCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $verificationCode = rand(1000, 9999);

        $request->authenticate();

        $user = $request->user();

        $accessToken = $user->createToken('personal-access-token')->accessToken;

        #TODO: check what is the purpose of mobileVerification()->mobile_number?
        
        $user->mobileVerification()->create([
            // 'mobile_number' => $user->mobileVerification()->mobile_number,
            'mobile_number' => $user->mobile_number,
            'code' => $verificationCode,
            'expires_at' => Carbon::now()->addSeconds(300),
        ]);

        // TODO: replace this email sending to mobile sending, temporary medium for sending verification code
        // TODO: remove the verification code from the response. 
        // TODO: when user is not authenticated, the user model should be removed. 

        Mail::to($user->email)->send(new SendMobileVerificationCode(['code' => $verificationCode]));

        return response()->json([
            'message' => 'success',
            'body' => [
                'message' => 'Verification code was sent to your mobile number.',
                'user' => $user,
                'verification_code' => $verificationCode, // return temporary in response
                'access_token' => $accessToken
            ]
        ], 201);
    }

    public function logout()
    {
        $accessToken = Auth::user()->token();

        $accessToken->revoke();

        return response()->noContent();
    }
}
