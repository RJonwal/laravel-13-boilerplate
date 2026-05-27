<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// ---------------- Guest API routes ----------------
Route::middleware('PreventBackHistory')->group(function () {
    require base_path('app/Domains/Api/Auth/Routes/auth.php');
});

// ---------------- Authenticated API routes ----------------
Route::middleware(['auth:sanctum', 'RestrictIP'])->group(function () {
    require base_path('app/Domains/Api/Common/Routes/common.php');  
});