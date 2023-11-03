<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Central\Auth\LoginController;
use App\Http\Controllers\Central\TenantManagementController;
use App\Http\Controllers\Central\UserManagementController;

Route::get('/', function () {
    return 'Central Domain';
});

Route::middleware(['guest'])->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('login', 'create');
        Route::post('login', 'store');
    });
});

Route::middleware(['web', 'auth'])->group(function () {

    Route::post('logout', [LoginController::class, 'destroy']);

    Route::prefix('super-admin')->group(function () {
        Route::get('/', function () {
            return 'super-admin dashboard page';
        });

        Route::controller(TenantManagementController::class)->group(function () {
            Route::get('tenants', 'index');
            Route::get('tenants/create', 'create');
            Route::post('tenants', 'store');
            Route::get('tenants/{tenant}', 'show');
            Route::get('tenants/{tenant}/edit', 'edit');
            Route::put('tenants/{tenant}', 'update');
            Route::delete('tenants/{tenant}', 'destroy');
        });

        Route::controller(UserManagementController::class)->group(function () {
            Route::get('users', 'index');
            Route::get('users/create', 'create');
            Route::post('users', 'store');
            Route::get('users/{user}', 'show');
            Route::get('users/{user}/edit', 'edit');
            Route::put('users/{user}', 'update');
            Route::delete('users/{user}', 'destroy');
        });
    });
});
