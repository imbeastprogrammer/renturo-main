<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Mail\Tenants\Auth\SendMobileVerificationCode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MobileVerification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Auth;

class VerifyMobileController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/v1/resend/mobile/verification",
     *     summary="Resend Mobile Verification Code",
     *     description="Request a new verification code to be sent to the user's mobile number. Rate limited to prevent abuse.",
     *     operationId="resendMobileVerification",
     *     tags={"Mobile Verification"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=201,
     *         description="Verification code sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="The verification code for your mobile has been sent to the number +639123456789.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Too many requests - Rate limited",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Too many request for verification code. Please retry after waiting for 300 seconds.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function store()
    {
        try {

            $verificationCode = rand(1000, 9999);
            $mobileNumber = Auth::user()->verified_mobile_no->mobile_number;

            if (Auth::user()->verified_mobile_no->expires_at > Carbon::now()) {
                return response()->json([
                    'message' => 'failed',
                    'body' => [
                        'message' => 'Too many request for verification code. Please retry after waiting for 300 seconds.'
                    ]
                ], 429);
            };

            Auth::user()->mobileVerification()->create([
                'mobile_number' => $mobileNumber,
                'code' => $verificationCode,
                'expires_at' => Carbon::now()->addSeconds(300)
            ]);

            // TODO: replace this email sending to mobile sending, temporary medium for sending verification code
            Mail::to(Auth::user()->email)->send(new SendMobileVerificationCode(['code' => $verificationCode]));

            return response()->json([
                'message' =>'success',
                'body' => [
                    'message' => 'The verification code for your mobile has been sent to the number ' . $mobileNumber . '.',
                ]
            ], 201);

        } catch (\Exception $e) { 
            Log::error($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/verify/mobile",
     *     summary="Verify Mobile Number",
     *     description="Verify the user's mobile number using the verification code sent via SMS/Email",
     *     operationId="verifyMobile",
     *     tags={"Mobile Verification"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code"},
     *             @OA\Property(property="code", type="string", example="1234", description="4-digit verification code")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mobile number verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Your mobile phone number has been successfully verified.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid or expired verification code",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="failed"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="The code is either not valid or has expired.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function update(Request $request)
    {
        try {
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
                    'message' => 'failed',
                    'body' => [
                       'message' => 'The code is either not valid or has expired.'
                    ]
                ], 422);
            }
    
            $verifiedCode->update([
                'verified_at' => Carbon::now()
            ]);
    
            return response()->json([
                'message' =>'success',
                'body' => [
                    'message' => 'Your mobile phone number has been successfully verified.'
                ]
            ], 200);
        } catch (\Exception $e) { 
            Log::error($e->getMessage());
        }
    }
}
