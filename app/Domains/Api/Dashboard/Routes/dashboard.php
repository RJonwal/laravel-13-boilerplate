<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Api\Dashboard\Controllers\DashboardController;
use App\Domains\Api\Dashboard\Controllers\NotificationController;
use App\Domains\Api\Dashboard\Controllers\QrController;


Route::get('/dashboard', [DashboardController::class, 'dashboard']);
Route::get('/get-rspvs-members', [DashboardController::class, 'getRSPVSMembers']);

///////////// Qr Code Routes///////////////////
// Route::post('/generate-qr', [DashboardController::class, 'generate']);
Route::post('/verify-qr', [DashboardController::class, 'verifyQRcode']);


///////////// Notification Routes ////////////////
Route::get('/notifications/activity', [NotificationController::class, 'notificationActivityList']);
Route::get('/notifications/scheduled', [NotificationController::class, 'notificationScheduledList']);
Route::get('/notifications/unread', [NotificationController::class, 'unread']);
Route::get('/notifications/read', [NotificationController::class, 'read']);
Route::post('/notifications/read/{id}', [NotificationController::class, 'markAsRead']);
// Route::post('/notifications/send', [NotificationController::class, 'send']);
Route::post('/notifications/create-schedule', [NotificationController::class, 'createSchedule']);
Route::get('/notifications/edit-scheduled/{id}', [NotificationController::class, 'editSchedule']);
Route::post('/notifications/update-schedule/{id}', [NotificationController::class, 'updateSchedule']);
Route::get('/notifications/target-members', [NotificationController::class, 'targetMembers']);
Route::get('/notifications/requested-members-list', [NotificationController::class, 'requestedMemberList']);

// Route::gety


