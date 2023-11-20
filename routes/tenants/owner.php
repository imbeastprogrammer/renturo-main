<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Middleware\RedirectIfTenantActivated;
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
        return Inertia::render('tenants/owner/dashboard/index');
    });
    Route::get('/post-management', function () {
        return Inertia::render('tenants/owner/post-management/index');
    });
    Route::get('/post-management/list-of-properties', function () {
        return Inertia::render('tenants/owner/post-management/list-of-properties/index');
    });
    Route::get('/post-management/analytics/ads', function () {
        return Inertia::render('tenants/owner/post-management/analytics/advertisements/index');
    });
    Route::get('/post-management/analytics/listings', function () {
        return Inertia::render('tenants/owner/post-management/analytics/listings/index');
    });
    Route::get('/post-management/analytics/promotions', function () {
        return Inertia::render('tenants/owner/post-management/analytics/promotions/index');
    });
    Route::get('/post-management/calendar', function () {
        return Inertia::render('tenants/owner/post-management/calendar/index');
    });
    Route::get('/user-management', function () {
        return Inertia::render('tenants/owner/user-management/index');
    });
    Route::get('/settings', function () {
        return Inertia::render('tenants/owner/settings/index');
    });
});
