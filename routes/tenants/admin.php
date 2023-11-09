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
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    RedirectIfTenantActivated::class
])->group(function () {

    Route::get('/', function () {
        return Inertia::render('tenants/admin/dashboard/index');
    });
    Route::get('/post', function () {
        return Inertia::render('tenants/admin/post/listings/index');
    });
    Route::get('/post/bookings', function () {
        return Inertia::render('tenants/admin/post/bookings/index');
    });
    Route::get('/post/categories', function () {
        return Inertia::render('tenants/admin/post/categories/index');
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
    Route::get('/settings', function () {
        return Inertia::render('tenants/admin/settings/index');
    });
    Route::get('/users/view/{userid}', function() {
        return Inertia::render('tenants/admin/users/view/index');
    });

    Route::resource('/users', UserManagementController::class);

    Route::resource('/posts', PostManagementController::class);
});
