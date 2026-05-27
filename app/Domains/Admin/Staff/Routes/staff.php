<?php

use App\Domains\Admin\Staff\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('system-users/change-password/{id}', [StaffController::class, "changePassword"])->name('system-users.change-password');
Route::post('system-users/change-password/{id}', [StaffController::class, "changePasswordSubmit"])->name('system-users.change-password');

Route::post('system-users/change-status', [StaffController::class, "changeStatus"])->name('system-users.status');
Route::resource('system-users', StaffController::class);