<?php

declare(strict_types=1);

use App\Http\Controllers\Tenants\Admin\PostController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Middleware\RedirectIfTenantActivated;

use App\Http\Controllers\Tenants\Admin\UserManagementController;
use App\Http\Controllers\Tenants\Admin\PostManagementController;

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
    Route::get('/post-management/list-of-properties', function () {
        return Inertia::render('tenants/admin/post-management/listings/index');
    });
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

    Route::get('/listings', function () {
        return Inertia::render('tenants/admin/listings/properties/index');
    });
    Route::get('/listings/for-approval', function () {
        return Inertia::render('tenants/admin/listings/for-approval/index');
    });
    Route::get('/listings/form-builder', function () {
        return Inertia::render('tenants/admin/listings/form-builder/index');
    });
    Route::get('/settings/personal-information', function () {
        return Inertia::render('tenants/admin/settings/personal-information/index');
    });
    Route::get('/settings/change-password', function () {
        return Inertia::render('tenants/admin/settings/change-password/index');
    });

    Route::post('/users', [UserManagementController::class, 'store']);
    Route::put('/users/{id}', [UserManagementController::class, 'update']);
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy']);
    Route::get('/user-management/users', [UserManagementController::class, 'index']);
    Route::get('/user-management/users/create', [UserManagementController::class, 'create']);
    Route::get('/user-management/users/update/{id}', [UserManagementController::class, 'edit']);

    // owners
    Route::get('/user-management/owners', function () {
        return Inertia::render('tenants/admin/user-management/owners/index');
    });
    Route::get('/user-management/owners/create', function () {
        return Inertia::render('tenants/admin/user-management/owners/create-owner/index');
    });
    Route::get('/user-management/owners/update/{id}', function () {
        return Inertia::render('tenants/admin/user-management/owners/update-owner/index');
    });

    // admins
    Route::get('/user-management/admins', function () {
        return Inertia::render('tenants/admin/user-management/admins/index');
    });
    Route::get('/user-management/admins/create', function () {
        return Inertia::render('tenants/admin/user-management/admins/create-admin/index');
    });
    Route::get('/user-management/admins/update/{id}', function () {
        return Inertia::render('tenants/admin/user-management/admins/update-admin/index');
    });

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

    Route::resource('/posts', PostManagementController::class);
});
