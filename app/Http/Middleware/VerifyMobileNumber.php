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
        // Check which guard has an authenticated user
        $user = null;
        $isApiRequest = false;
        
        // Try API guard first (if user is authenticated via token)
        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
            $isApiRequest = true;
        }
        // Otherwise, try web guard (session-based authentication)
        elseif (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $isApiRequest = false;
        }
        
        // If no user is authenticated on either guard
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
            if ($isApiRequest || $request->expectsJson()) {
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
