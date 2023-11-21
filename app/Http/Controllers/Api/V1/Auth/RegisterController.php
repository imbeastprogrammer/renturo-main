<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\Tenants\Auth\RegisterRequest;
use App\Mail\Tenants\Auth\SendMobileVerificationCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Carbon\Carbon;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {

            $verificationCode = rand(1000, 9999);

            $user = User::create($request->validated());

            if (!$user) { 
                return response()->json([
                  'message' => 'failed',
                  'data' => [
                    'message' => 'Failed to create user. Please try again after few minutes.'
                  ]
                ], 400);
            }

            $user->mobileVerification()->create([
                'mobile_number' => $request->mobile_number,
                'code' => $verificationCode,
                'expires_at' => Carbon::now()->addSeconds(300),
            ]);

            $accessToken = $user->createToken('personal-access-token')->accessToken;

            event(new Registered($user));

            Mail::to($user->email)->send(new SendMobileVerificationCode([
                'code' => $verificationCode
            ]));

            #TODO: Remove verification code and access token for security 
            return response()->json([
                'message' => 'success',
                'body' => [
                    'message' => 'Registration is successful!',
                    'verification_code' => $verificationCode,
                    'access_token' => $accessToken
                ]
            ], 201);

        } catch (\Exception $e) { 
            Log::error($e->getMessage());
        }
    }
}
