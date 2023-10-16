<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Middleware\RedirectIfTenantActivated;

use App\Http\Controllers\Tenants\Admin\UserManagementController;

use Inertia\Inertia;

Route::middleware([
    'web',
    'auth',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    RedirectIfTenantActivated::class
])->group(function () {

    Route::get('/', function () {
        return Inertia::render('admin/dashboard/index');
    });
    Route::get('/post', function () {
        return Inertia::render('admin/post/listings/index');
    });
    Route::get('/post/bookings', function () {
        return Inertia::render('admin/post/bookings/index');
    });
    Route::get('/post/categories', function () {
        return Inertia::render('admin/post/categories/index');
    });
    Route::get('/listings', function () {
        return Inertia::render('admin/listings/properties/index');
    });
    Route::get('/listings/for-approval', function () {
        return Inertia::render('admin/listings/for-approval/index');
    });
    Route::get('/listings/form-builder', function () {
        return Inertia::render('admin/listings/form-builder/index');
    });
    Route::get('/settings', function () {
        return Inertia::render('admin/settings/index');
    });
    Route::get('/users/view/{userid}', function() {
        return Inertia::render('admin/users/view/index');
    });

    Route::resource('/users', UserManagementController::class);
});
