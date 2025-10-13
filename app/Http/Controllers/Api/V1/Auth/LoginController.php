<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Mail\Tenants\Auth\SendMobileVerificationCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="User Login",
     *     description="Authenticate user and return access token with OTP sent to mobile",
     *     operationId="login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="owner@main.renturo.test", description="Email, phone number, or username"),
     *             @OA\Property(property="password", type="string", format="password", example="password", description="User password (minimum 8 characters)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Login successful, OTP sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="body",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Verification code was sent to your mobile number."),
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="email", type="string", example="owner@main.renturo.test"),
     *                     @OA\Property(property="username", type="string", example="beastowner1234"),
     *                     @OA\Property(property="role", type="string", example="OWNER"),
     *                     @OA\Property(property="mobile_number", type="string", example="+639123456789")
     *                 ),
     *                 @OA\Property(property="verification_code", type="string", example="1234", description="4-digit OTP (temporary in response, will be removed)"),
     *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGc...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $verificationCode = rand(1000, 9999);

        $request->authenticate();

        $user = $request->user();

        // Create token with configured TTL
        $tokenTTL = config('passport.token_ttl', 60); // default 60 minutes
        $token = $user->createToken('personal-access-token', [], Carbon::now()->addMinutes($tokenTTL));
        $accessToken = $token->token;
        $accessToken->expires_at = Carbon::now()->addMinutes($tokenTTL);
        $accessToken->save();

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
                'access_token' => $token->accessToken
            ]
        ], 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/logout",
     *     summary="User Logout",
     *     description="Revoke the current user's access token",
     *     operationId="logout",
     *     tags={"Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=204,
     *         description="Successfully logged out"
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
    public function logout()
    {
        $user = Auth::guard('api')->user();
        $token = $user->token();

        // Revoke the current token
        $token->revoke();

        return response()->noContent();
    }
}
