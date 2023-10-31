<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Central\Auth\LoginController;
use App\Http\Controllers\Central\UserManagementController;

Route::get('/', function () {
    return 'Central Domain';
});

Route::prefix('super-admin')->group(function () {
    Route::get('login', [LoginController::class, 'create']);
    Route::post('login', [LoginController::class, 'store']);

    Route::middleware('auth')->group(function () {
        Route::post('logout', [LoginController::class, 'destroy']);

        Route::get('/', function () {
            return 'super-admin dashboard page';
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
