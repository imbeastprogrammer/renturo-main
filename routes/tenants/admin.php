<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Middleware\RedirectIfTenantActivated;
use Inertia\Inertia;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    RedirectIfTenantActivated::class
])->group(function () {

    Route::get('/', function () {
        return Inertia::render('admin/index');
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
    Route::get('/settings', function () {
        return Inertia::render('admin/settings/index');
    });
    Route::get('/users', function () {
        return Inertia::render('admin/users/list/index');
    });
    Route::get('/users/create', function () {
        return Inertia::render('admin/users/create/index');
    });
});
