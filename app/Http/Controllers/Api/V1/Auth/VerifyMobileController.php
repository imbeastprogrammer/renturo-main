<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileVerification;
use Carbon\Carbon;
use Auth;

class VerifyMobileController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $verifiedCode = MobileVerification::where('mobile_no', Auth::user()->verified_mobile_no->mobile_no)
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
