<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Api\Common\Controllers\CommonController;


Route::prefix('common')->group(function () {
    Route::get('/terms-privacy', [CommonController::class, 'getPrivacyAndTerms']);
    Route::get('/help-and-support', [CommonController::class, 'helpAndSupport']);
});

