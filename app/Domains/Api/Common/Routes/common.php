<?php
use App\Domains\Api\Common\Controllers\CommonController;
use Illuminate\Support\Facades\Route;

Route::get('dashboard', [CommonController::class, 'dashboard']);
?>
