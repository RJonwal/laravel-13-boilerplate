<?php

use App\Domains\Admin\User\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('users/change-password/{id}', [UserController::class, "changePassword"])->name('users.change-password');
Route::post('users/change-password/{id}', [UserController::class, "changePasswordSubmit"])->name('users.change-password');

Route::post('users/change-status', [UserController::class, "changeStatus"])->name('users.status');
Route::post('users/isapproved', [UserController::class, "isHostApproved"])->name('users.isapproved');
Route::resource('users', UserController::class);

