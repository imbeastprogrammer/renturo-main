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
        if (!Auth::user()->verified_mobile_no->verified_at) {
            // return response()->json([
            //     'message' => 'failed',
            //     'body' => [
            //         'message' => 'The mobile number has not been verified yet. Please check your mobile for the verification code.'
            //     ]
            // ], 403);

            // Redirect to the otp verification page
            return redirect('/login/otp');
        }

        return $next($request);
    }
}
