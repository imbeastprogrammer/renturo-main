<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Middleware\RedirectIfTenantActivated;

use App\Http\Controllers\Api\V1\Tenants\User\DynamicFormSubmissionController;

Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    RedirectIfTenantActivated::class
])
    ->prefix('v1')
    ->group(function () { 
        // Route::post('login', [LoginController::class, 'login']);
        // Route::post('register', [RegisterController::class, 'register']);

        // Route::middleware('auth:api')->group(function () {
        //     Route::put('/verify/mobile', [VerifyMobileController::class, 'update']);
        //     Route::post('/resend/mobile/verification', [VerifyMobileController::class, 'store']);
        // });
              
        Route::middleware(['auth:api', 'verifiedMobileNumber'])->group(function () {
            // Route::get('/user', function (Request $request) {
            //     return $request->user();
            // });
            // Route::put('/password', [PasswordController::class, 'update']);
            // Route::delete('logout', [LoginController::class, 'logout']);

            Route::get('/forms', [DynamicFormSubmissionController::class, 'index']);
            Route::get('/form/{formId}', [DynamicFormSubmissionController::class, 'show']);
        });
    });
