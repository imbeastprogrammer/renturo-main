<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EncryptionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });


Route::get('/', function () {
    return 'Central Domain';
});

Route::get('/encrypt', [CryptographyController::class, 'encrypt']);
Route::get('/decrypt', [CryptographyController::class, 'decrypt']);

Route::get('/login', function () {
    return Inertia::render('login/index');
});
Route::get('/register', function () {
    return Inertia::render('register/index');
});
Route::get('/forgot-password', function () {
    return Inertia::render('forgot-password/index');
});
Route::get('/admin', function () {
    return Inertia::render('admin/index');
});
Route::get('/admin/post', function () {
    return Inertia::render('admin/post/listings/index');
});
Route::get('/admin/post/bookings', function () {
    return Inertia::render('admin/post/bookings/index');
});
Route::get('/admin/post/categories', function () {
    return Inertia::render('admin/post/categories/index');
});
Route::get('/admin/settings', function () {
    return Inertia::render('admin/settings/index');
});
Route::get('/admin/users', function () {
    return Inertia::render('admin/users/list/index');
});
Route::get('/admin/users/create', function () {
    return Inertia::render('admin/users/create/index');
});
    





// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified']);

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit']);
//     Route::patch('/profile', [ProfileController::class, 'update']);
//     Route::delete('/profile', [ProfileController::class, 'destroy']);
// });

// require __DIR__ . '/auth.php';
