<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyMobileNumber
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('api')->user();
        
        // If user is null, it means token is invalid/expired/revoked
        // The auth:api middleware should have already rejected this,
        // but we check again for safety
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated'
                ], 401);
            }
            return redirect('/login');
        }

        $verification = $user->verified_mobile_no;
        if (!$verification || !$verification->verified_at) {
            // For API requests, return JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'failed',
                    'body' => [
                        'message' => 'The mobile number has not been verified yet. Please check your mobile for the verification code.'
                    ]
                ], 403);
            }

            // For web requests, redirect to OTP verification page
            return redirect('/login/otp');
        }

        return $next($request);
    }
}
