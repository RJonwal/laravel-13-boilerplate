<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Api\Profile\Controllers\ProfileController;

Route::group(['prefix' => 'profile'], function () {
        
    Route::get('/', [ProfileController::class, 'getProfile']);
    Route::post('/', [ProfileController::class, 'updateProfile']);

    // Email OTP Routes
    Route::post('/email/send-otp', [ProfileController::class, 'sendEmailOtp']);
    Route::post('/email/verify-otp', [ProfileController::class, 'verifyEmailOtp']);

    // Phone OTP Routes  
    Route::post('/phone/send-otp', [ProfileController::class, 'sendPhoneOtp']);
    Route::post('/phone/verify-otp', [ProfileController::class, 'verifyPhoneOtp']);

    Route::post('/update-password', [ProfileController::class, 'updatePassword']);
    
    Route::post('/destroy', [ProfileController::class, 'destroy']);
});




