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
use App\Http\Controllers\Tenants\Admin\CategoryManagementController;
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

    Route::get('/settings/personal-information', function () {
        return Inertia::render('tenants/admin/settings/personal-information/index');
    });
    Route::get('/settings/change-password', function () {
        return Inertia::render('tenants/admin/settings/change-password/index');
    });

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

    // sub owners
    Route::get('/user-management/sub-owners', function () {
        return Inertia::render('tenants/admin/user-management/sub-owners/index');
    });
    Route::get('/user-management/sub-owners/create', function () {
        return Inertia::render('tenants/admin/user-management/sub-owners/create-sub-owner/index');
    });
    Route::get('/user-management/sub-owners/update/{id}', function () {
        return Inertia::render('tenants/admin/user-management/sub-owners/update-sub-owner/index');
    });

    // post management
    Route::resource('/posts', PostManagementController::class);
    Route::get('/post-management/list-of-properties', [PostManagementController::class, 'index']);
    Route::get('/post-management/bookings', function () {
        return Inertia::render('tenants/admin/post-management/bookings/index');
    });
    Route::get('/post-management/categories', function () {
        return Inertia::render('tenants/admin/post-management/categories/index');
    });
    Route::get('/post-management/promotions', function () {
        return Inertia::render('tenants/admin/post-management/promotions/index');
    });
    Route::get('/post-management/promotions/{id}', function () {
        return Inertia::render('tenants/admin/post-management/promotions/view-promotion/index');
    });
    Route::get('/post-management/ads', function () {
        return Inertia::render('tenants/admin/post-management/ads/index');
    });

    Route::put('/sort/form/pages', [DynamicFormPageController::class, 'sortFormPages']);
    Route::resource('/form/pages', DynamicFormPageController::class);

    Route::put('/sort/form/fields', [DynamicFormFieldController::class, 'sortFormFields']);
    Route::resource('/form/fields', DynamicFormFieldController::class);

    Route::resource('/categories', CategoryManagementController::class);

    Route::resource('/sub-categories', SubCategoryManagementController::class);
});
