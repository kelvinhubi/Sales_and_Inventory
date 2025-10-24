<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\OrderController;
// Auth Controllers
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BrandController;
// Main Controllers
use App\Http\Controllers\login;
use App\Http\Controllers\MainController;
// Global Controllers
use App\Http\Controllers\Manager\BrandController as ManagerBrandController;
use App\Http\Controllers\Manager\DiscrepancyReportController as ManagerDiscrepancyReportController;
use App\Http\Controllers\Manager\ExpenseController as ManagerExpenseController;
use App\Http\Controllers\Manager\ManagerController as ManagerManagerController;
// API Controllers
use App\Http\Controllers\Manager\OrderController as ManagerOrderController;
use App\Http\Controllers\Manager\PastOrderController as ManagerPastOrderController;
use App\Http\Controllers\Manager\PastOrdersSummaryController as ManagerPastOrdersSummaryController;
// Owner Controllers
use App\Http\Controllers\Manager\ProductController as ManagerProductController;
use App\Http\Controllers\Manager\RejectedGoodsController as ManagerRejectedGoodsController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\Owner\DiscrepancyReportController;
// Manager Controllers
use App\Http\Controllers\Owner\ExpenseController as OwnerExpenseController;
use App\Http\Controllers\Owner\PastOrderController;
use App\Http\Controllers\Owner\PastOrdersSummaryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RejectedGoodsController;
use App\Http\Controllers\signup;
use App\Http\Controllers\UserHeartbeatController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

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
    if (! User::where('email', 'Owner@example.com')->exists()) {
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
Route::get('logout', [login::class, 'logout'])->name('logout');

// Rate-limited authentication routes (10 attempts per minute for login only)
Route::middleware('throttle:10,1')->group(function () {
    Route::post('login', [login::class, 'loginUser'])->name('loginUser');
    Route::post('signup', [signup::class, 'createUser'])->name('createUser');
});

// Logout should NOT be rate limited - users should always be able to logout
Route::post('logout', [login::class, 'logout'])->name('logout');

// Password reset with separate, less aggressive rate limiting
Route::middleware('throttle:3,1')->group(function () {
    Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('password/update', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

// Password Reset Form Routes (no rate limit on viewing)
Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');

// Password Reset Testing Route (Remove in production)
Route::get('/test-password-reset', function () {
    // Check if all required components exist
    $checks = [
        'Password Reset Token Table' => \Schema::hasTable('password_reset_tokens'),
        'User Model Exists' => class_exists(\App\Models\User::class),
        'ForgotPasswordController Exists' => class_exists(\App\Http\Controllers\Auth\ForgotPasswordController::class),
        'ResetPasswordController Exists' => class_exists(\App\Http\Controllers\Auth\ResetPasswordController::class),
        'Mail Configuration' => ! empty(config('mail.mailers.smtp')),
        'Password Reset Config' => ! empty(config('auth.passwords.users')),
    ];

    // Test user creation
    $testUser = \App\Models\User::first();
    if (! $testUser) {
        $checks['Test User Available'] = false;
    } else {
        $checks['Test User Available'] = true;
        $checks['Test User Email'] = $testUser->email;
    }

    // Check mail configuration
    $mailConfig = [
        'MAIL_MAILER' => env('MAIL_MAILER'),
        'MAIL_HOST' => env('MAIL_HOST'),
        'MAIL_PORT' => env('MAIL_PORT'),
        'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
    ];

    return view('password-reset-test', compact('checks', 'mailConfig'));
})->name('test.password.reset');

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

Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
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

    // Expenses (Owner)
    Route::get('expenses', [OwnerExpenseController::class, 'showView'])->name('expenses');

    //Suppliers
    Route::get('suppliers', [App\Http\Controllers\SupplierController::class, 'showView'])->name('suppliers');
    Route::get('api/suppliers', [App\Http\Controllers\SupplierController::class, 'index'])->name('api.suppliers');

    // Activity Logs (Owner - can view all logs)
    Route::get('logs', function () {
        return view('owner.logs');
    })->name('logs');

});

/*
|--------------------------------------------------------------------------
| Manager Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
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

    // Expenses
    Route::get('expenses', [ManagerExpenseController::class, 'showView'])->name('expenses');

    // Activity Logs (Manager - can view only their own logs)
    Route::get('logs', function () {
        return view('manager.logs');
    })->name('logs');

    //Suppliers
    Route::get('suppliers', [App\Http\Controllers\SupplierController::class, 'showView2'])->name('suppliers');
    Route::get('api/suppliers', [App\Http\Controllers\SupplierController::class, 'index'])->name('api.suppliers');
});
