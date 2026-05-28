<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Api\Common\Controllers\CommonController;


Route::prefix('common')->group(function () {
    Route::get('/venue-types', [CommonController::class, 'getVenueTypes']);
    Route::get('/terms-privacy', [CommonController::class, 'getPrivacyAndTerms']);
    Route::get('/early-access', [CommonController::class, 'getEarlyAccess']);
});
Route::group([
    'prefix' => 'common',
    'middleware' => ['auth:sanctum']
], function () {        
    Route::put('/update-password', [CommonController::class, 'updatePassword'])
        ->name('host.profile.update');
        
});