<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Middleware\RedirectIfTenantActivated;

use App\Http\Controllers\Api\V1\Tenants\Client\StoreController;
use App\Http\Controllers\Api\V1\Tenants\Client\DynamicFormController;
use App\Http\Controllers\Api\V1\Tenants\Client\DynamicFormSubmissionController;
use App\Http\Controllers\Api\V1\Tenants\Client\BankController;
use App\Http\Controllers\API\V1\Tenants\Client\CategoryController;

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

            Route::resource('/store', StoreController::class);
            Route::get('/store/user/{userId}', [StoreController::class, 'getUserStores']);

            Route::get('/form/{formId}', [DynamicFormController::class, 'show']);

            Route::resource('/forms', DynamicFormSubmissionController::class);
            Route::post('/forms/{formId}/submit', [DynamicFormSubmissionController::class, 'submit']);
            Route::get('/forms/user/{userId}', [DynamicFormSubmissionController::class, 'getUserSubmission']);
            Route::get('/forms/user/{userId}/form/{formId}', [DynamicFormSubmissionController::class, 'getUserFormSubmission']);

            Route::resource('/banks', BankController::class);
            Route::get('/user/banks/', [BankController::class, 'getUserBanks']);

            Route::get('/categories', [CategoryController::class, 'index']);
            Route::get('/categories/search', [CategoryController::class, 'search']);

        });
    });
