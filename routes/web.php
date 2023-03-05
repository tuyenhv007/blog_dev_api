<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

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

Route::get('/',function () {
    return view('welcome');
});
Route::get('/csrf', [UserController::class, 'getCsrfToken']);
Route::post('/user/signup', [UserController::class, 'signUp']);
Route::post('/user/active', [UserController::class, 'activeAccount']);
Route::post('/user/otp_resend', [UserController::class, 'resendOtp']);
Route::post('/auth/login', [AuthController::class, 'signIn']);
