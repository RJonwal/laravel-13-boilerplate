<?php

use App\Domains\Admin\Dashboard\Controllers\DashboardController;
use App\Domains\Admin\Dashboard\Controllers\ProfileController;
use App\Domains\Admin\Dashboard\Controllers\PendingReceiptController;
use Illuminate\Support\Facades\Route;

Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

// Profile 
Route::get('profile', [ProfileController::class, 'showProfile'])->name('show.profile');
Route::post('profile', [ProfileController::class, 'updateProfile'])->name('update.profile');
Route::post('remove-profile-image', [ProfileController::class, 'removeProfileImage'])->name('remove.profile-image');

Route::post('change-password', [ProfileController::class, 'updateChangePassword'])->name('update.change.password');

Route::get('get-receipt-amount', [DashboardController::class, 'getReceiptAmount'])->name('dashboard.get-receipt-amount');
Route::get('get-top-customers', [DashboardController::class, 'getTopCustomers'])->name('dashboard.get-top-customers');
Route::get('get-receipt-charts', [DashboardController::class, 'getReceiptChart'])->name('dashboard.get-receipt-chart');
Route::get('chart/cash-sales/export', [DashboardController::class, 'exportCashSales'])->name('dashboard.cash-sales.export');
Route::post('dashboard/toggle-ledger-session', [DashboardController::class, 'toggleLedgerSession'])
    ->name('dashboard.toggle-ledger-session');


Route::get('pending-receipt-customer-data/{bucket}', [PendingReceiptController::class, 'pendingReceiptCustomerData'])->name('dashboard.pending-receipt-customer-data');
Route::get('pending-receipt/customer/view/{bucket}/{uuid}', [PendingReceiptController::class, 'viewPendingReceiptCustomer'])->name('dashboard.pending-receipt.customer');
Route::get('dashboard/pending-receipt-customer-table', [PendingReceiptController::class, 'getPendingReceiptCustomerData'])->name('dashboard.pending-receipt-customer-table');

// PDF's
Route::get('dashboard/pending-receipt/export/{bucket}', [PendingReceiptController::class, 'exportPendingReceiptCustomers'])->name('dashboard.pending-receipt.export');
Route::get('dashboard/customer/ledgers/export', [PendingReceiptController::class, 'exportLegersOfCustomer'])->name('dashboard.customer.ledgers.export');    


// Password Check and Get Ledger Remaining Amount
Route::post('dashboard/check-admin-password', [DashboardController::class, 'checkAdminPassword'])->name('dashboard.check-admin-password');
Route::post('ledger/{customer}/whatsapp', [DashboardController::class, 'sendWhatsapp'])->name('ledger.whatsapp.send');