<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileVerification;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Auth;

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

        return back()->with([
            'success' => 'The verification code for your mobile has been sent to the number ' . $mobileNumber . '.',
            'verification_code' => $verificationCode
        ]);
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

        if (Auth::user()->role === User::ROLE_ADMIN) {
            return redirect()->to(RouteServiceProvider::ADMIN_HOME);
        } else if (Auth::user()->role === User::ROLE_OWNER) {
            return redirect()->to(RouteServiceProvider::OWNER_HOME);
        } else if (Auth::user()->role === User::ROLE_USER) {
            return redirect()->to(RouteServiceProvider::USER_HOME);
        } else {
            return redirect()->to(RouteServiceProvider::SUPER_ADMIN_HOME);
        }
    }
}
