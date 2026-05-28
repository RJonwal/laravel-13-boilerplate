<?php

use App\Domains\Api\Auth\Controllers\ForgotPasswordController;
use App\Domains\Api\Auth\Controllers\LoginController;
use App\Domains\Api\Auth\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::post('register', [RegisterController::class, 'register']);

Route::post('login', [LoginController::class, 'login']);
Route::post('login/request-otp', [LoginController::class, 'requestLoginOtp']);
Route::post('login/verify-otp', [LoginController::class, 'verifyLoginOtp']);
Route::post('login/resend-otp', [LoginController::class, 'resendLoginOtp']);

Route::post('forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('password/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('password/reset-password', [ForgotPasswordController::class, 'resetPassword']);

// Logout
Route::post('logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');