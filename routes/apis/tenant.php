<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Middleware\RedirectIfTenantActivated;

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\PasswordController;
use App\Http\Controllers\Api\V1\Auth\VerifyMobileController;

use App\Http\Controllers\Api\V1\Tenants\StoreController;

use Illuminate\Support\Facades\Password;

Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    RedirectIfTenantActivated::class
])
    ->prefix('v1')
    ->group(function () { 
        Route::post('login', [LoginController::class, 'login']);
        Route::post('register', [RegisterController::class, 'register']);

        Route::middleware('auth:api')->group(function () {
            Route::put('/verify/mobile', [VerifyMobileController::class, 'update']);
            Route::post('/resend/mobile/verification', [VerifyMobileController::class, 'store']);
        });
              
        Route::middleware(['auth:api', 'verifiedMobileNumber'])->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
            Route::put('/password', [PasswordController::class, 'update']);
            Route::delete('logout', [LoginController::class, 'logout']);

            // Route::get('/store', [StoreController::class, 'index']);
            // Route::post('/store', [StoreController::class, 'store']);
            // Route::put('/store/{id}', [StoreController::class, 'update']);
            // Route::get('/store/{id}', [StoreController::class, 'show']);
        });
    });
