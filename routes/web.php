<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Central\Auth\LoginController;
use App\Http\Controllers\Central\TenantManagementController;
use App\Http\Controllers\Central\UserManagementController;
use Inertia\Inertia;

Route::get('/', function () {
    return 'Central Domain';
});

Route::get('/login/enter-otp', function () {
    return Inertia::render("central/login-otp/index");
});
Route::get('/forgot-password', function () {
    return Inertia::render("central/forgot-password/index");
});
Route::get('/forgot-password/enter-otp', function () {
    return Inertia::render("central/forgot-password-otp/index");
});
Route::get('/create-new-password', function () {
    return Inertia::render("central/create-new-password/index");
});


Route::middleware('guest:central')->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('login', 'create');
        Route::post('login', 'store');
    });
});

Route::middleware('auth:central')->group(function () {

    Route::post('logout', [LoginController::class, 'destroy']);

    Route::prefix('super-admin')->group(function () {
        Route::get('/', function () {
            return 'super admin dashboard';
        });
        Route::get('/dashboard', function () {
            return Inertia::render('central/super-admin/dashboard/index');
        });
        Route::get('/administration/roles', function () {
            return Inertia::render('central/super-admin/administration/roles/index');
        });
        Route::get('/administration/roles/add', function () {
            return Inertia::render('central/super-admin/administration/roles/add-role/index');
        });
        Route::get('/administration/roles/edit/{id}', function () {
            return Inertia::render('central/super-admin/administration/roles/edit-role/index');
        });
        Route::get('/settings', function () {
            return Inertia::render('central/super-admin/settings/index');
        });


        Route::controller(TenantManagementController::class)->group(function () {
            Route::get('site-management/tenants', 'index');
            Route::get('site-management/tenants/create', 'create');
            Route::get('site-management/tenants/{tenant}', 'show');
            Route::get('site-management/tenants/edit/{tenant}', 'edit');
            Route::post('tenants', 'store');
            Route::put('tenants/{tenant}', 'update');
            Route::delete('tenants/{tenant}', 'destroy');
        });

        Route::controller(UserManagementController::class)->group(function () {
            Route::get('administration/user-management', 'index');
            Route::get('administration/user-management/add', 'create');
            Route::get('administration/user-management/show/{user}', 'show');
            Route::get('administration/user-management/edit/{user}', 'edit');
            Route::post('users', 'store');
            Route::put('users/{user}', 'update');
            Route::delete('users/{user}', 'destroy');
        });
    });
});
