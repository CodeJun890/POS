<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ManagerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//LOGIN ROUTES
Route::get('/', [AuthController::class, 'viewLogin'])->name('login.get');
Route::post('/login-post', [AuthController::class, 'postLogin'])->name('login.post');

// =============> CASHIER ROUTES <=================
Route::get('/cashier-dashboard', [CashierController::class, 'viewCashierDashboard'])->name('cashier-dashboard.get');
// CASHIER ORDER
Route::get('/cashier-order', [CashierController::class, 'viewCashierOrder'])->name('cashier-order.get');
Route::post('/send/cashier-order', [CashierController::class, 'postCashierOrder'])->name('cashier-order.post');
// CASHIER ORDER HISTORY
Route::get('/cashier-order-history', [CashierController::class, 'viewCashierOrderHistory'])->name('cashier-order-history.get');
Route::get('/cashier-order-history/filter', [CashierController::class, 'filterCashierOrderHistory'])->name('cashier-order-history-filter.get');
Route::get('/receipt/view', [CashierController::class, 'viewReceipt'])->name('receipt.get');
Route::get('/download-receipt/{id}', [CashierController::class, 'downloadReceipt'])->name('download.receipt');
// CASHIER PENDING ORDER
Route::get('/orders/pending', [CashierController::class, 'getPendingOrders']);
Route::put('/orders/update/{orderId}', [CashierController::class, 'updateGroupStatus']);
Route::delete('/orders/cancel/{orderGroupId}', [CashierController::class, 'cancelOrder']);


// CASHIER PROFILE
Route::get('/cashier-profile', [CashierController::class, 'viewCashierProfile'])->name('cashier-profile.get');



// =============> MANAGER ROUTES <=================
Route::get('/manager-dashboard', [ManagerController::class, 'viewManagerDashboard'])->name('manager-dashboard.get');
Route::get('/get-weekly-data', [ManagerController::class, 'getWeeklyData']);
// MANAGER ORDER HISTORY
Route::get('/manager-order-history', [ManagerController::class, 'viewManagerOrderHistory'])->name('manager-order-history.get');
Route::get('/manager-order-history/filter', [ManagerController::class, 'filterManagerOrderHistory'])->name('manager-order-history-filter.get');
Route::get('/manager-receipt/view', [ManagerController::class, 'viewReceipt'])->name('manager-receipt.get');
// MANAGER'S CASHIER MANAGEMENT
Route::get('/cashier-management', [ManagerController::class, 'viewCashierManagement'])->name('cashier-management.get');
Route::delete('/manager/cashier/delete/{id}', [ManagerController::class, 'deleteCashier'])->name('cashier.delete');
Route::post('/manager/cashier/create', [ManagerController::class, 'createCashier'])->name('cashier.create');
// MANAGER'S CASHIER MANAGEMENT
Route::get('/branch-management', [ManagerController::class, 'viewBranchManagement'])->name('branch-management.get');
Route::delete('/manager/branch/delete/{id}', [ManagerController::class, 'deleteBranch'])->name('branch.delete');
Route::post('/manager/branch/store', [ManagerController::class, 'createBranch'])->name('branch-management.post');







// LOGOUT ROUTE
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');
