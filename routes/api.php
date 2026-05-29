<?php

use Illuminate\Support\Facades\Route;


// ---------------- Guest API routes ----------------
Route::middleware('PreventBackHistory')->group(function () {
    require base_path('app/Domains/Api/Auth/Routes/auth.php');
    require base_path('app/Domains/Api/Common/Routes/common.php');
});

// ---------------- Authenticated API routes ----------------
Route::middleware(['auth:sanctum'])->group(function () {
    require base_path('app/Domains/Api/Dashboard/Routes/dashboard.php');
    require base_path('app/Domains/Api/Profile/Routes/profile.php');
});