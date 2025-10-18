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
use App\Http\Controllers\API\V1\Tenants\Client\SubCategoryController;
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

            Route::resource('/store', StoreController::class);
            Route::get('/store/{storeId}', [StoreController::class, 'show']);
            Route::get('/stores/user/{userId}', [StoreController::class, 'getUserStores']);

            // Dynamic Forms Routes
            Route::prefix('forms')->group(function () {
                // Form Templates
                Route::get('/', [DynamicFormController::class, 'index']);
                Route::get('/subcategory/{subcategoryId}', [DynamicFormController::class, 'getBySubcategory']);
                Route::get('/{formId}', [DynamicFormController::class, 'show']);

                // Form Pages
                Route::prefix('pages')->group(function () {
                    Route::get('/', [DynamicFormPageController::class, 'index']);
                    Route::post('/', [DynamicFormPageController::class, 'store']);
                    Route::get('/{pageId}', [DynamicFormPageController::class, 'show']);
                    Route::put('/{pageId}', [DynamicFormPageController::class, 'update']);
                    Route::delete('/{pageId}', [DynamicFormPageController::class, 'destroy']);
                    Route::post('/reorder', [DynamicFormPageController::class, 'reorder']);
                });

                // Form Fields
                Route::prefix('fields')->group(function () {
                    Route::get('/', [DynamicFormFieldController::class, 'index']);
                    Route::post('/', [DynamicFormFieldController::class, 'store']);
                    Route::get('/{fieldId}', [DynamicFormFieldController::class, 'show']);
                    Route::put('/{fieldId}', [DynamicFormFieldController::class, 'update']);
                    Route::delete('/{fieldId}', [DynamicFormFieldController::class, 'destroy']);
                    Route::post('/reorder', [DynamicFormFieldController::class, 'reorder']);
                });

                // Form Submissions
                Route::post('/{formId}/submit', [DynamicFormSubmissionController::class, 'submit']);
                Route::get('/user/{userId}', [DynamicFormSubmissionController::class, 'getUserDynamicFormSubmissions']);
                Route::get('/user/{userId}/store/{storeId}', [DynamicFormSubmissionController::class, 'getUserDynamicFormSubmissionByStoreId']);
                Route::get('/user/{userId}/form/{formId}', [DynamicFormSubmissionController::class, 'getUserDynamicFormSubmissionByFormId']);
                Route::delete('/submissions/{submissionId}', [DynamicFormSubmissionController::class, 'destroy']);
            });
            
            Route::resource('/banks', BankController::class);
            Route::get('/user/banks/', [BankController::class, 'getUserBanks']);

            // Categories & SubCategories
            Route::get('/categories/search', [CategoryController::class, 'search']);
            Route::resource('/categories', CategoryController::class);
            Route::get('/categories/{categoryId}/subcategories', [SubCategoryController::class, 'getByCategory']);
            Route::resource('/subcategories', SubCategoryController::class);

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
