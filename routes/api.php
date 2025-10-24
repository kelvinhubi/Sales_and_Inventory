<?php

use App\Http\Controllers\Api\AIInsightsController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\UserHeartbeatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Manager routes with web middleware for session support (InfinityFree compatibility)
Route::middleware(['web', 'auth'])->group(function () {
    Route::apiResource('managers', App\Http\Controllers\ManagerController::class);
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::apiResource('brands', BrandController::class);
    Route::get('brands/{brand}/standard-items', [BrandController::class, 'getStandardItems']);
    Route::put('brands/{brand}/standard-items', [BrandController::class, 'updateStandardItems']);
    Route::apiResource('brands.branches', BranchController::class);
    Route::apiResource('products', App\Http\Controllers\Api\ProductController::class);
    Route::post('products/delete-expired', [App\Http\Controllers\Api\ProductController::class, 'deleteExpiredProducts']);

    // Rejected goods routes
    Route::post('rejected-goods', [App\Http\Controllers\RejectedGoodController::class, 'store']);
    Route::get('rejected-goods', [App\Http\Controllers\RejectedGoodController::class, 'index']);
});

// Orders and Expenses routes - require authentication
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('orders/final-summary', [OrderController::class, 'finalSummary']);
    Route::get('orders/statistics', [OrderController::class, 'statistics']);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('expenses', ExpenseController::class);
    
    Route::get('branches', function () {
        return response()->json([
            'data' => \App\Models\Branch::with('brand')->get()->map(function ($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'brand_name' => $branch->brand->name,
                ];
            }),
        ]);
    });

    Route::get('productss', function () {
        return response()->json([
            'data' => \App\Models\Product::all(['id', 'name', 'category', 'quantity', 'price']),
        ]);
    });
});

// Heartbeat routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/heartbeat', [UserHeartbeatController::class, 'update']);
    Route::get('/online-users', [UserHeartbeatController::class, 'getOnlineUsers']);
});

// Analytics and Dashboard routes - require authentication
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('analytics/sales-data', [App\Http\Controllers\Api\AnalyticsController::class, 'getSalesData']);
    Route::get('analytics/product-sales', [App\Http\Controllers\Api\AnalyticsController::class, 'getProductSalesData']);
    Route::get('analytics/top-bottom-brands', [App\Http\Controllers\Api\AnalyticsController::class, 'getTopBottomBrands']);
    Route::get('analytics/top-bottom-branches', [App\Http\Controllers\Api\AnalyticsController::class, 'getTopBottomBranches']);
    Route::get('analytics/top-bottom-products', [App\Http\Controllers\Api\AnalyticsController::class, 'getTopBottomProducts']);

    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics']);
    Route::get('/dashboard/brands', [DashboardController::class, 'brands']);
    Route::get('/dashboard/branches', [DashboardController::class, 'getBranches']);
    Route::get('/dashboard/products', [DashboardController::class, 'getProducts']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// AI Business Intelligence routes - require authentication
Route::middleware(['web', 'auth'])->prefix('ai')->group(function () {
    Route::get('/insights', [AIInsightsController::class, 'getInsights']);
    Route::get('/recommendations/inventory', [AIInsightsController::class, 'getInventoryRecommendations']);
    Route::post('/ask', [AIInsightsController::class, 'askQuestion']);
    Route::get('/health-score', [AIInsightsController::class, 'getHealthScore']);
    Route::get('/daily-brief', [AIInsightsController::class, 'getDailyBrief']);
    Route::post('/clear-cache', [AIInsightsController::class, 'clearCache']);
});

// Supplier API routes - require authentication
Route::middleware(['web', 'auth'])->group(function () {
    Route::apiResource('suppliers', App\Http\Controllers\SupplierController::class);
});

// Activity Logs API routes - require authentication
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('activity-logs/statistics', [App\Http\Controllers\Api\ActivityLogController::class, 'statistics']);
    Route::get('activity-logs/my-logs', [App\Http\Controllers\Api\ActivityLogController::class, 'myLogs']);
    Route::get('activity-logs/export', [App\Http\Controllers\Api\ActivityLogController::class, 'export']);
    Route::get('activity-logs', [App\Http\Controllers\Api\ActivityLogController::class, 'index']);
});
