<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Api\Profile\Controllers\ProfileController;

Route::group([
    'prefix' => 'profile',
    'middleware' => ['auth:sanctum']
], function () {
        
    Route::get('/', [ProfileController::class, 'getProfile']);
    Route::get('/getProfilebyHostMember/{user_uuid}', [ProfileController::class, 'getProfilebyHostMember']);
    Route::post('/', [ProfileController::class, 'updateProfile']);
    // Email OTP Routes
    Route::post('/email/send-otp', [ProfileController::class, 'sendEmailOtp']);
    Route::post('/email/verify-otp', [ProfileController::class, 'verifyEmailOtp']);

    // Phone OTP Routes  
    Route::post('/phone/send-otp', [ProfileController::class, 'sendPhoneOtp']);
    Route::post('/phone/verify-otp', [ProfileController::class, 'verifyPhoneOtp']);

    // Block Unblock Member
    
});
// Route::post('/block-unblock-member', [ProfileController::class, 'blockUnblockMember']);
Route::get('/block-list', [ProfileController::class, 'blockList']);
Route::post('/member/destroy', [ProfileController::class, 'destroy']);

// Help & Support
Route::get('/help-and-support', [ProfileController::class, 'helpAndSupport']);
Route::post('/user/pause-account', [ProfileController::class, 'togglePauseAccount']);




