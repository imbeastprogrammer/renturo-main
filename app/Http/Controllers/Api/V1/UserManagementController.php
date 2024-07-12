<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Mail\Tenants\Auth\SendMobileVerificationCode;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function retrieve(Request $request)
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

    public function updateMPIN(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mpin' => 'required|digits:4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Retrieve the ID of the currently authenticated user
        $userId = auth()->id();

        // Encrypt MPIN before storing it for security reasons
        $encryptedMPIN = bcrypt($request->input('mpin'));

        // Update the user's MPIN securely with the update method
        $updated = User::where('id', $userId)->update([
            'mpin' => $encryptedMPIN
        ]);

        if ($updated) {
            return response()->json([
                'message' => 'success',
                'body' => [
                    'message' => 'MPIN has successfully updated!',
                    'data' => [],
                ]
            ], 200);
            
        } else {
            return response()->json([
                'message' => 'failed',
                'errors' => 'Unable to update MPIN',
            ], 422);
        }
    }

    public function getMPIN(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mpin' => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Retrieve the authenticated user
        $user = $request->user();

        // Check if the MPIN matches
        if (!empty($user->mpin) && Hash::check($request->input('mpin'), $user->mpin)) {
            return response()->json([
                'message' => 'success',
                'body' => [
                    'message' => 'MPIN has successfully verified!',
                    'data' => [],
                ]
            ], 200);
        } else {
            return response()->json([
                'message' => 'failed',
                'errors' => 'Invalid code. Please try again.',
            ], 422);
        }
    }

    public function resetMPIN(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mpin' => 'required|digits:4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Retrieve the ID of the currently authenticated user
        $userId = auth()->id();

        // Encrypt MPIN before storing it for security reasons
        $encryptedMPIN = bcrypt($request->input('mpin'));

        // Update the user's MPIN securely with the update method
        $updated = User::where('id', $userId)->update([
            'mpin' => $encryptedMPIN
        ]);

        if ($updated) {
            return response()->json([
                'message' => 'success',
                'body' => [
                    'message' => 'MPIN has successfully updated!',
                    'data' => [],
                ]
            ], 200);
            
        } else {
            return response()->json([
                'message' => 'failed',
                'errors' => 'Unable to update MPIN',
            ], 422);
        }
    }
}
