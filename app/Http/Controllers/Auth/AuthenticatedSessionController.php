<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenants\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Mail\Tenants\Auth\SendMobileVerificationCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;
use App\Models\User;
use Mail;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('tenants/login/index', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $verificationCode = rand(1000, 9999);

        $request->authenticate('web');

        $request->session()->regenerate();

        $user = $request->user();

        $user->mobileVerification()->create([
            'mobile_number' => $user->verified_mobile_no->mobile_number,
            'code' => $verificationCode,
            'expires_at' => Carbon::now()->addSeconds(300),
        ]);

        Mail::to($user->email)->send(new SendMobileVerificationCode(['code' => $verificationCode]));

        if (Auth::user()->role === User::ROLE_ADMIN) {
            return redirect()->intended(RouteServiceProvider::ADMIN_HOME);
        } else if (Auth::user()->role === User::ROLE_OWNER) {
            return redirect()->intended(RouteServiceProvider::OWNER_HOME);
        } else if (Auth::user()->role === User::ROLE_USER) {
            return redirect()->intended(RouteServiceProvider::USER_HOME);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
