<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Mail\Tenants\Auth\SendMobileVerificationCode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileVerification;
use Carbon\Carbon;
use Auth;
use Mail;

class VerifyMobileController extends Controller
{
    public function store()
    {
        $verificationCode = rand(1000, 9999);
        $mobileNumber = Auth::user()->verified_mobile_no->mobile_number;

        if (Auth::user()->verified_mobile_no->expires_at > Carbon::now()) {
            return response()->json([
                'message' => 'Too many request for verification code. Please retry after waiting for 300 seconds.'
            ], 429);
        };

        Auth::user()->mobileVerification()->create([
            'mobile_number' => $mobileNumber,
            'code' => $verificationCode,
            'expires_at' => Carbon::now()->addSeconds(300)
        ]);

        // TO DO: replace this email sending to mobile sending, temporary medium for sending verification code
        Mail::to(Auth::user()->email)->send(new SendMobileVerificationCode(['code' => $verificationCode]));

        return response()->json([
            'message' => 'The verification code for your mobile has been sent to the number ' . $mobileNumber . '.',
            'verification_code' => $verificationCode // return temporary in response
        ], 201);
    }

    public function update(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $verifiedCode = MobileVerification::where('mobile_number', Auth::user()->verified_mobile_no->mobile_number)
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->whereNull('verified_at')
            ->first();

        if (!$verifiedCode) {
            return response()->json([
                'message' => 'The code is either not valid or has expired.'
            ], 422);
        }

        $verifiedCode->update([
            'verified_at' => Carbon::now()
        ]);

        return response()->json([
            'message' => 'Your mobile phone number has been successfully verified.'
        ], 200);
    }
}
