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
use App\Http\Controllers\API\V1\Tenants\Client\MessageController;
use App\Http\Controllers\API\V1\Tenants\Client\ChatController;
use App\Http\Controllers\API\V1\Tenants\Client\DynamicFormAvailabilityController;

use App\Http\Controllers\API\V1\ImageUploadController;
use Database\Factories\ImageFactory;

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

            Route::post('/store', [StoreController::class, 'store']);
            Route::put('/store/{id}', [StoreController::class, 'update']);
            Route::get('/store/{id}', [StoreController::class, 'show']);
            Route::get('/store/{storeId}', [StoreController::class, 'show']);
            Route::get('/user/stores', [StoreController::class, 'getUserStores']);

            Route::get('/form/{formId}', [DynamicFormController::class, 'show']);

            Route::resource('/forms', DynamicFormSubmissionController::class);
            Route::post('/forms/{formId}/submit', [DynamicFormSubmissionController::class, 'submit']);
            Route::get('/forms/user/{userId}', [DynamicFormSubmissionController::class, 'getUserSubmission']);
            Route::get('/forms/user/{userId}/form/{formId}', [DynamicFormSubmissionController::class, 'getUserFormSubmission']);

            Route::resource('/banks', BankController::class);
            Route::get('/user/banks/', [BankController::class, 'getUserBanks']);

            Route::get('/categories', [CategoryController::class, 'index']);
            Route::get('/categories/search', [CategoryController::class, 'search']);

            Route::resource('/chats', ChatController::class);
            Route::delete('/chats/{chat}/leave', [ChatController::class, 'leaveChat']);
            Route::delete('/chats/{chat}/delete', [ChatController::class, 'deleteChat']);
            Route::post('/chats/{chat}/add/participants', [ChatController::class, 'addParticipants']);
            Route::delete('/chats/{chat}/remove/participants', [ChatController::class, 'removeParticipants']);
            Route::post('/chats/{chat}/typing', [ChatController::class, 'userTyping']);

            Route::post('/messages', [MessageController::class, 'sendMessage']);
            Route::put('/messages/{message}/read', [MessageController::class, 'markAsRead']);
            Route::get('/messages/{chatId}/get', [MessageController::class, 'getMessages']);
            Route::delete('/messages/{message}/delete', [MessageController::class, 'deleteMessage']);
            
            Route::post('/messages/file-upload', [ImageUploadController::class, 'upload']);

            Route::resource('/form-availability', DynamicFormAvailabilityController::class);
        });
    });
