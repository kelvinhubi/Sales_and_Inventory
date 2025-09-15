<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\UserHeartbeatController;



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
Route::apiResource('managers', App\Http\Controllers\ManagerController::class);
Route::apiResource('brands', BrandController::class);
Route::get('brands/{brand}/standard-items', [BrandController::class, 'getStandardItems']);
Route::put('brands/{brand}/standard-items', [BrandController::class, 'updateStandardItems']);
Route::apiResource('brands.branches', BranchController::class);
Route::apiResource('products', App\Http\Controllers\Api\ProductController::class);
Route::post('products/delete-expired', [App\Http\Controllers\Api\ProductController::class, 'deleteExpiredProducts']);

// Rejected goods routes
Route::post('rejected-goods', [App\Http\Controllers\RejectedGoodController::class, 'store']);
Route::get('rejected-goods', [App\Http\Controllers\RejectedGoodController::class, 'index']);

Route::get('orders/final-summary', [OrderController::class, 'finalSummary']);
Route::get('orders/statistics', [OrderController::class, 'statistics']);
Route::apiResource('orders', OrderController::class);
Route::get('branches', function() {
    return response()->json([
        'data' => \App\Models\Branch::with('brand')->get()->map(function($branch) {
            return [
                'id' => $branch->id,
                'name' => $branch->name,
                'brand_name' => $branch->brand->name
            ];
        })
    ]);
});
Route::get('productss', function() {
    return response()->json([
        'data' => \App\Models\Product::all(['id', 'name','quantity', 'price'])
    ]);
});

// Heartbeat routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/heartbeat', [UserHeartbeatController::class, 'update']);
    Route::get('/online-users', [UserHeartbeatController::class, 'getOnlineUsers']);
});



Route::get('analytics/sales-data', [App\Http\Controllers\Api\AnalyticsController::class, 'getSalesData']);
Route::get('analytics/product-sales', [App\Http\Controllers\Api\AnalyticsController::class, 'getProductSalesData']);
Route::get('analytics/top-bottom-brands', [App\Http\Controllers\Api\AnalyticsController::class, 'getTopBottomBrands']);
Route::get('analytics/top-bottom-branches', [App\Http\Controllers\Api\AnalyticsController::class, 'getTopBottomBranches']);
Route::get('analytics/top-bottom-products', [App\Http\Controllers\Api\AnalyticsController::class, 'getTopBottomProducts']);


Route::get('/dashboard/analytics', [DashboardController::class, 'analytics']);
Route::get('/dashboard/brands', [DashboardController::class, 'brands']);
Route::get('/dashboard/branches', [DashboardController::class, 'getBranches']);
Route::get('/dashboard/products', [DashboardController::class, 'getProducts']);
Route::middleware(['web', 'auth'])->get('/user', function (Request $request) {
    return $request->user();
});