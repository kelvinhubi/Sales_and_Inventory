<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

// Auth Controllers
use App\Http\Controllers\login;
use App\Http\Controllers\signup;

// Main Controllers
use App\Http\Controllers\MainController;
use App\Http\Controllers\ManagerController;

// Global Controllers
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RejectedGoodsController;

// API Controllers
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\UserHeartbeatController;

// Owner Controllers
use App\Http\Controllers\Owner\PastOrderController;
use App\Http\Controllers\Owner\DiscrepancyReportController;
use App\Http\Controllers\Owner\PastOrdersSummaryController;

// Manager Controllers
use App\Http\Controllers\Manager\ProductController as ManagerProductController;
use App\Http\Controllers\Manager\BrandController as ManagerBrandController;
use App\Http\Controllers\Manager\OrderController as ManagerOrderController;
use App\Http\Controllers\Manager\ManagerController as ManagerManagerController;
use App\Http\Controllers\Manager\PastOrderController as ManagerPastOrderController;
use App\Http\Controllers\Manager\RejectedGoodsController as ManagerRejectedGoodsController;
use App\Http\Controllers\Manager\DiscrepancyReportController as ManagerDiscrepancyReportController;
use App\Http\Controllers\Manager\PastOrdersSummaryController as ManagerPastOrdersSummaryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Homepage and Root Routes
Route::get('root', function () {
    return view('index');
})->name('root');

Route::get('/', function () {
    if (!User::where('email', 'Owner@example.com')->exists()) {
        User::create([
            'name' => 'Owner',
            'email' => 'Owner@example.com',
            'password' => bcrypt('12345678'),
            'Role' => 'Owner',
            'is_online' => false,
            'last_activity' => now(),
            'phone' => '1234567890',
            'notes' => 'Owner',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    return view('index');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('signup', [signup::class, 'showForm'])->name('Signup');
Route::get('login', [login::class, 'showForm'])->name('Login');
Route::post('login', [login::class, 'loginUser'])->name('loginUser');
Route::post('logout', [login::class, 'logout'])->name('logout');
Route::post('signup', [signup::class, 'createUser'])->name('createUser');

// Password Reset Routes
Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/update', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

/*
|--------------------------------------------------------------------------
| Dashboard Routes (Public Access)
|--------------------------------------------------------------------------
*/

Route::get('customer', [MainController::class, 'customer'])->name('customer');
Route::get('manager', [MainController::class, 'manager'])->name('manager');
Route::get('owner', [MainController::class, 'owner'])->name('owner');

/*
|--------------------------------------------------------------------------
| Password Change Routes
|--------------------------------------------------------------------------
*/

Route::get('owner/change-password', [MainController::class, 'ownerChangePassword'])->name('owner.password.edit');
Route::put('change-password', [MainController::class, 'ownerUpdatePassword'])->name('owner.password.update');
Route::get('manager/change-password', [MainController::class, 'managerChangePassword'])->name('manager.password.edit');
Route::put('manager/change-password', [MainController::class, 'managerUpdatePassword'])->name('manager.password.update');

/*
|--------------------------------------------------------------------------
| Global API Routes
|--------------------------------------------------------------------------
*/

Route::get('api/analytics', [DashboardController::class, 'analytics']);
Route::get('api/brands', [BrandController::class, 'index']);
Route::get('api/branches', [BranchController::class, 'allBranches']);
Route::get('api/products', [ProductController::class, 'index']);
Route::post('api/orders/deduct-inventory', [OrderController::class, 'deductInventory'])->name('owner.orders.deduct-inventory');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

// Heartbeat Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/heartbeat', [UserHeartbeatController::class, 'update'])->name('heartbeat');
    Route::get('/online-users', [UserHeartbeatController::class, 'getOnlineUsers'])->name('online-users');
});

/*
|--------------------------------------------------------------------------
| Owner Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('owner')->name('owner.')->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'showView'])->name('dashboard');
    
    // Basic Pages
    Route::get('products', [ProductController::class, 'showView'])->name('products');
    Route::get('brand', [BrandController::class, 'showView'])->name('brands');
    Route::get('orders', [OrderController::class, 'showView'])->name('orders');
    Route::get('managers', [ManagerController::class, 'showView'])->name('managers');
    
    // Discrepancy Report
    Route::get('discrepancy-report', [DiscrepancyReportController::class, 'index'])->name('discrepancy-report.index');
    Route::get('discrepancy-report/generate', [DiscrepancyReportController::class, 'generate'])->name('discrepancy-report.generate');
    
    // Rejected Goods
    Route::get('rejected-goods/dr-details/{drNumber}', [RejectedGoodsController::class, 'getDrDetails'])->name('rejected-goods.drDetails');
    Route::resource('rejected-goods', RejectedGoodsController::class);
    
    // Past Orders
    Route::get('past-orders/export-selected', [PastOrderController::class, 'exportSelected'])->name('past-orders.exportSelected');
    Route::post('past-orders/delete-selected', [PastOrderController::class, 'deleteSelected'])->name('past-orders.deleteSelected');
    Route::get('past-orders/test-delete', [PastOrderController::class, 'testDelete'])->name('past-orders.testDelete');
    Route::get('past-orders/summary-report', [PastOrdersSummaryController::class, 'exportSummaryReport'])->name('past-orders.summaryReport');
    Route::resource('past-orders', PastOrderController::class);
});

/*
|--------------------------------------------------------------------------
| Manager Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('manager')->name('manager.')->group(function () {
    // Basic Pages
    Route::get('products', [ManagerProductController::class, 'showView'])->name('products');
    Route::get('brands', [ManagerBrandController::class, 'showView'])->name('brands');
    Route::get('orders', [ManagerOrderController::class, 'showView'])->name('orders');
    Route::get('managers', [ManagerManagerController::class, 'showView'])->name('managers');
    
    // Order Management
    Route::post('orders/deduct-inventory', [ManagerOrderController::class, 'deductInventory'])->name('orders.deduct-inventory');
    
    // Discrepancy Report
    Route::get('discrepancy-report', [ManagerDiscrepancyReportController::class, 'index'])->name('discrepancy-report.index');
    Route::get('discrepancy-report/generate', [ManagerDiscrepancyReportController::class, 'generate'])->name('discrepancy-report.generate');
    
    // Rejected Goods
    Route::get('rejected-goods/dr-details/{drNumber}', [ManagerRejectedGoodsController::class, 'getDrDetails'])->name('rejected-goods.drDetails');
    Route::resource('rejected-goods', ManagerRejectedGoodsController::class);
    
    // Past Orders
    Route::get('past-orders/export-selected', [ManagerPastOrderController::class, 'exportSelected'])->name('past-orders.exportSelected');
    Route::post('past-orders/delete-selected', [ManagerPastOrderController::class, 'deleteSelected'])->name('past-orders.deleteSelected');
    Route::get('past-orders/test-delete', [ManagerPastOrderController::class, 'testDelete'])->name('past-orders.testDelete');
    Route::get('past-orders/summary-report', [ManagerPastOrdersSummaryController::class, 'exportSummaryReport'])->name('past-orders.summaryReport');
    Route::resource('past-orders', ManagerPastOrderController::class);
});
