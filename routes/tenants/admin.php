<?php

declare(strict_types=1);

use App\Http\Controllers\Tenants\Admin\PostController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Middleware\RedirectIfTenantActivated;

use App\Http\Controllers\Tenants\Admin\UserManagementController;
use App\Http\Controllers\Tenants\Admin\PostManagementController;
use App\Http\Controllers\Tenants\Admin\DynamicFormFieldController;
use App\Http\Controllers\Tenants\Admin\DynamicFormPageController;
use App\Http\Controllers\Tenants\Admin\DynamicFormController;
use App\Http\Controllers\Tenants\Admin\CategoryManagementController;
use App\Http\Controllers\Tenants\Admin\FormBuilderController;
use App\Http\Controllers\Tenants\Admin\PostManagementAdsController;
use App\Http\Controllers\Tenants\Admin\PostManagementBookingsController;
use App\Http\Controllers\Tenants\Admin\PostManagementCategoriesController;
use App\Http\Controllers\Tenants\Admin\PostManagementPromotionsController;
use App\Http\Controllers\Tenants\Admin\PostManagementPropertiesController;
use App\Http\Controllers\Tenants\Admin\ReportsController;
use App\Http\Controllers\Tenants\Admin\SettingsManagementController;
use App\Http\Controllers\Tenants\Admin\SubCategoryManagementController;

use Inertia\Inertia;

Route::middleware([
    'web',
    'auth',
    'verifiedMobileNumber',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    RedirectIfTenantActivated::class
])->group(function () {

    Route::get('/', function () {
        return Inertia::render('tenants/admin/dashboard/index');
    });

    Route::get('/settings/personal-information', [SettingsManagementController::class, 'personalInformation']);
    Route::get('/settings/change-password', [SettingsManagementController::class, 'changePassword']);

    // user management
    Route::post('/users', [UserManagementController::class, 'store']);
    Route::put('/users/{id}', [UserManagementController::class, 'update']);
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy']);
    Route::get('/user-management/users', [UserManagementController::class, 'getUsers']);
    Route::get('/user-management/users/create', [UserManagementController::class, 'createUser']);
    Route::get('/user-management/users/update/{id}', [UserManagementController::class, 'editUser']);

    Route::get('/user-management/admins', [UserManagementController::class, 'getAdmins']);
    Route::get('/user-management/admins/create', [UserManagementController::class, 'createAdmin']);
    Route::get('/user-management/admins/update/{id}', [UserManagementController::class, 'editAdmin']);

    Route::get('/user-management/owners', [UserManagementController::class, 'getOwners']);
    Route::get('/user-management/owners/create', [UserManagementController::class, 'createOwner']);
    Route::get('/user-management/owners/update/{id}', [UserManagementController::class, 'editOwner']);

    Route::get('/user-management/sub-owners', [UserManagementController::class, 'getSubOwners']);
    Route::get('/user-management/sub-owners/create', [UserManagementController::class, 'createSubOwner']);
    Route::get('/user-management/sub-owners/update/{id}', [UserManagementController::class, 'editSubOwner']);

    Route::get('/user-management/reports', [ReportsController::class, 'index']);
    Route::get('/user-management/reports/{id}', [ReportsController::class, 'show']);

    Route::resource('/posts', PostManagementController::class);
    Route::get('/post-management/properties', [PostManagementPropertiesController::class, 'index']);
    Route::get('/post-management/bookings', [PostManagementBookingsController::class, 'index']);

    Route::get('/post-management/categories', [CategoryManagementController::class, 'index']);
    Route::post('/categories/restore/{id}', [CategoryManagementController::class, 'restore']);
    Route::resource('/categories', CategoryManagementController::class);

    Route::get('/post-management/sub-categories', [SubCategoryManagementController::class, 'index']);
    Route::resource('/sub-categories', SubCategoryManagementController::class);
    Route::post('/sub-categories/restore/{id}', [SubCategoryManagementController::class, 'restore']);

    Route::get('/post-management/promotions', [PostManagementPromotionsController::class, 'index']);
    Route::get('/post-management/promotions/{id}', [PostManagementPromotionsController::class, 'edit']);
    Route::get('/post-management/ads', [PostManagementAdsController::class, 'index']);
    Route::get('/post-management/form-builder', [FormBuilderController::class, 'index']);

    Route::put('/sort/form/pages', [DynamicFormPageController::class, 'sortFormPages']);
    Route::post('/form/pages/restore/{id}', [DynamicFormPageController::class, 'restore']);
    Route::resource('/form/pages', DynamicFormPageController::class);

    Route::post('/form/fields/restore/{id}', [DynamicFormFieldController::class, 'restore']);
    Route::resource('/form/fields', DynamicFormFieldController::class);



    Route::post('/form/restore/{id}', [DynamicFormController::class, 'restore']);
    Route::get('/form/all/{id}', [DynamicFormController::class, 'getFormPagesAndFields']);
    Route::put('/form/all/{id}', [DynamicFormController::class, 'updateFormPagesAndFields']);
    Route::resource('/form', DynamicFormController::class);
});
