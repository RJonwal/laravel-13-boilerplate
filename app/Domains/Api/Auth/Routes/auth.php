<?php

use App\Domains\Api\Auth\Controllers\LoginController;
use Illuminate\Support\Facades\Route;


Route::post('login', [LoginController::class, 'login']);
Route::post('login/verify-pin', [LoginController::class, 'verifyPin']);

Route::post('logout', [LoginController::class, 'logout'])->middleware(['auth:sanctum', 'RestrictIP']);

