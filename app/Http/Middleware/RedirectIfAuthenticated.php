<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if ($guard === 'central') {
                    return redirect()->intended(RouteServiceProvider::SUPER_ADMIN_HOME);
                }

                if (Auth::user()->role === User::ROLE_ADMIN) {
                    return redirect()->intended(RouteServiceProvider::ADMIN_HOME);
                } else if (Auth::user()->role === User::ROLE_OWNER) {
                    return redirect()->intended(RouteServiceProvider::OWNER_HOME);
                } else if (Auth::user()->role === User::ROLE_USER) {
                    return redirect()->intended(RouteServiceProvider::USER_HOME);
                }
            }
        }

        return $next($request);
    }
}
