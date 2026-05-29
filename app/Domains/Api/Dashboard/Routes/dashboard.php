<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Api\Dashboard\Controllers\DashboardController;


Route::get('/dashboard', [DashboardController::class, 'dashboard']);