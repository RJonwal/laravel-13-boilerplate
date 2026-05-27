<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('login');
});


/* Route::middleware(['PreventBackHistory', 'guest'])->group(function () {
    require base_path('app/Domains/Admin/Auth/Routes/auth.php');
}); */

// Auth Routes
require base_path('app/Domains/Admin/Auth/Routes/auth.php');

Route::middleware(['auth', 'PreventBackHistory', 'RestrictIP'])
->group(function () {
    // Add more route files as needed
    require base_path('app/Domains/Admin/Dashboard/Routes/dashboard.php');
    require base_path('app/Domains/Admin/Setting/Routes/setting.php');
    require base_path('app/Domains/Admin/Role/Routes/role.php');
    require base_path('app/Domains/Admin/Staff/Routes/staff.php');
});