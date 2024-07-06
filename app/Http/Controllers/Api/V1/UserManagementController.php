<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Mail\Tenants\Auth\SendMobileVerificationCode;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class UserManagementController extends Controller
{
    public function retrieveUser(Request $request)
    {
        $input = $request->input('username');

        $validator = Validator::make($request->all(), [
            'username' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'Username is required.',
                'data' => $validator->errors(),
            ], 422);
        }

        // Determine the type of input and set the appropriate validation rule
        $fieldType = filter_var($input, FILTER_VALIDATE_EMAIL) ? 'email' :
                     (is_numeric($input) ? 'mobile_number' : 'username');

        // Check if the input exists in the database
        $user = User::where($fieldType, $input)->first();

        if (!$user) {
            return response()->json([
                'message' => 'failed',
                'errors' => 'User not found.',
            ], 404);
        }

        $verificationCode = rand(1000, 9999);

        $accessToken = $user->createToken('personal-access-token')->accessToken;
        
        $user->mobileVerification()->create([
            // 'mobile_number' => $user->mobileVerification()->mobile_number,
            'mobile_number' => $user->mobile_number,
            'code' => $verificationCode,
            'expires_at' => Carbon::now()->addSeconds(300),
        ]);

        // Mail::to($user->email)->send(new SendMobileVerificationCode([
        //     'code' => $verificationCode
        // ]));

        return response()->json([
            'message' => 'success',
            'body' => [
                'message' => 'User has been successfully retrieved!',
                'user' => $user,
                'verification_code' => $verificationCode, // return temporary in response
                'access_token' => $accessToken
            ]
        ], 200);
    }
}
