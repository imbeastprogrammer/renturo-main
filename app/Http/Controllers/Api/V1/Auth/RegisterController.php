<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\Tenants\Auth\RegisterRequest;
use App\Mail\Tenants\Auth\SendMobileVerificationCode;
use App\Models\User;
use Carbon\Carbon;
use Mail;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $verificationCode = rand(1000, 9999);

        $user = User::create($request->safe()->except(['mobile_no']));

        $user->mobileVerification()->create([
            'mobile_no' => $request->mobile_no,
            'code' => $verificationCode,
            'expires_at' => Carbon::now()->addSeconds(300),
        ]);

        $accessToken = $user->createToken('personal-access-token')->accessToken;

        event(new Registered($user));

        Mail::to($user->email)->send(new SendMobileVerificationCode(['code' => $verificationCode]));

        return response()->json([
            'message' => 'Registration complete!',
            'verification_code' => $verificationCode, // return temporary in response
            'access_token' => $accessToken
        ], 201);
    }
}
