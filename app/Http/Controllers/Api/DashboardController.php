<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Expense;
use App\Models\PastOrder;
use App\Models\PastOrderItem;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get analytics data for the dashboard.
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $year = $request->get('year', Carbon::now()->year);
            $month = $request->get('month');
            $day = $request->get('day');

            // Start with a base query on PastOrder that applies year filter
            $baseQuery = PastOrder::query()->whereYear('created_at', $year);
            if ($month) {
                $baseQuery->whereMonth('created_at', $month);
            }
            if ($day) {
                $baseQuery->whereDay('created_at', $day);
            }

            // Apply brand and branch filters
            if ($request->filled('brand_id')) {
                $baseQuery->where('brand_id', $request->brand_id);
            }
            if ($request->filled('branch_id')) {
                $baseQuery->where('branch_id', $request->branch_id);
            }
            if ($request->filled('product_id')) {
                $baseQuery->whereHas('items', function ($q) use ($request) {
                    $q->where('product_id', $request->product_id);
                });
            }

            // Get the filtered orders to be used for all calculations
            $filteredOrders = $baseQuery->get();

            // Calculate totals from the filtered orders
            $totalSalesThisYear = $filteredOrders->sum('total_amount');
            $totalOrdersThisYear = $filteredOrders->count();

            // Calculate monthly totals for the graph
            $graphData = $filteredOrders->groupBy(function ($order) {
                return Carbon::parse($order->created_at)->format('M');
            })->map(function ($group) {
                return $group->sum('total_amount');
            });

            // Calculate most orders and total sales for the current month from filtered orders
            $mostOrdersThisMonth = $filteredOrders
                ->filter(function ($order) {
                    return Carbon::parse($order->created_at)->month === Carbon::now()->month;
                })
                ->count();

            $totalSalesThisMonth = $filteredOrders
                ->filter(function ($order) {
                    return Carbon::parse($order->created_at)->month === Carbon::now()->month;
                })
                ->sum('total_amount');

            // Find the top and bottom products based on the filtered orders' IDs
            $pastOrderIds = $filteredOrders->pluck('id');


            // Find top 10 products
            $top10Products = PastOrderItem::select('past_order_items.product_id', DB::raw('SUM(past_order_items.quantity) as total_quantity_sold'), 'products.name')
                ->join('products', 'past_order_items.product_id', '=', 'products.id')
                ->whereIn('past_order_id', $pastOrderIds)
                ->groupBy('past_order_items.product_id', 'products.name')
                ->orderByDesc('total_quantity_sold')
                ->take(10)
                ->get();

            // Find bottom 10 products
            $bottom10Products = PastOrderItem::select('past_order_items.product_id', DB::raw('SUM(past_order_items.quantity) as total_quantity_sold'), 'products.name')
                ->join('products', 'past_order_items.product_id', '=', 'products.id')
                ->whereIn('past_order_id', $pastOrderIds)
                ->groupBy('past_order_items.product_id', 'products.name')
                ->orderBy('total_quantity_sold')
                ->take(10)
                ->get();

            $productSales = PastOrderItem::select('past_order_items.product_id', DB::raw('SUM(past_order_items.quantity * price) as total_sales'))
            ->whereIn('past_order_id', $pastOrderIds)
            ->with('product:id,name')
            ->groupBy('product_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->product->name => $item->total_sales];
            });
            // Total revenue loss from rejected goods
            $revenueLossQuery = DB::table('rejected_goods')->whereYear('date', $year);
            if ($month) {
                $revenueLossQuery->whereMonth('date', $month);
            }
            if ($day) {
                $revenueLossQuery->whereDay('date', $day);
            }
            if ($request->filled('branch_id')) {
                $revenueLossQuery->where('branch_id', $request->branch_id);
            }
            if ($request->filled('brand_id')) {
                $revenueLossQuery->where('brand_id', $request->brand_id);
            }
            $revenueLoss = $revenueLossQuery->sum('amount');

            // Average order value
            $averageOrderValue = $totalOrdersThisYear > 0 ? round($totalSalesThisYear / $totalOrdersThisYear, 2) : 0;

            // Sales per store (branch)
            $salesPerStore = Branch::all()->mapWithKeys(function ($branch) use ($year, $month, $day, $request) {
                $query = PastOrder::where('branch_id', $branch->id)->whereYear('created_at', $year);
                if ($month) {
                    $query->whereMonth('created_at', $month);
                }
                if ($day) {
                    $query->whereDay('created_at', $day);
                }
                if ($request->filled('brand_id')) {
                    $query->where('brand_id', $request->brand_id);
                }
                $sum = $query->sum('total_amount');

                return [$branch->name => $sum];
            });
            if ($salesPerStore->isEmpty()) {
                $salesPerStore = collect(['No Data' => 0]);
            }

            // Sales per brand
            $salesPerBrand = Brand::all()->mapWithKeys(function ($brand) use ($year, $month, $day, $request) {
                $query = PastOrder::where('brand_id', $brand->id)->whereYear('created_at', $year);
                if ($month) {
                    $query->whereMonth('created_at', $month);
                }
                if ($day) {
                    $query->whereDay('created_at', $day);
                }
                if ($request->filled('branch_id')) {
                    $query->where('branch_id', $request->branch_id);
                }
                $sum = $query->sum('total_amount');

                return [$brand->name => $sum];
            });
            if ($salesPerBrand->isEmpty()) {
                $salesPerBrand = collect(['No Data' => 0]);
            }

            // Orders per store
            $ordersPerStore = Branch::all()->mapWithKeys(function ($branch) use ($year, $month, $day, $request) {
                $query = PastOrder::where('branch_id', $branch->id)->whereYear('created_at', $year);
                if ($month) {
                    $query->whereMonth('created_at', $month);
                }
                if ($day) {
                    $query->whereDay('created_at', $day);
                }
                if ($request->filled('brand_id')) {
                    $query->where('brand_id', $request->brand_id);
                }
                $count = $query->count();

                return [$branch->name => $count];
            });
            if ($ordersPerStore->isEmpty()) {
                $ordersPerStore = collect(['No Data' => 0]);
            }

            // Orders per brand
            $ordersPerBrand = Brand::all()->mapWithKeys(function ($brand) use ($year, $month, $day, $request) {
                $query = PastOrder::where('brand_id', $brand->id)->whereYear('created_at', $year);
                if ($month) {
                    $query->whereMonth('created_at', $month);
                }
                if ($day) {
                    $query->whereDay('created_at', $day);
                }
                if ($request->filled('branch_id')) {
                    $query->where('branch_id', $request->branch_id);
                }
                $count = $query->count();

                return [$brand->name => $count];
            });
            if ($ordersPerBrand->isEmpty()) {
                $ordersPerBrand = collect(['No Data' => 0]);
            }

            // Revenue loss per product (rejected goods)
            $revenueLossPerProduct = Product::all()->mapWithKeys(function ($product) use ($year, $month, $day, $request) {
                $sum = $product->rejectedGoodItems()
                    ->whereHas('rejectedGood', function ($q) use ($year, $month, $day, $request) {
                        $q->whereYear('date', $year);
                        if ($month) {
                            $q->whereMonth('date', $month);
                        }
                        if ($day) {
                            $q->whereDay('date', $day);
                        }
                        if ($request->filled('branch_id')) {
                            $q->where('branch_id', $request->branch_id);
                        }
                        if ($request->filled('brand_id')) {
                            $q->where('brand_id', $request->brand_id);
                        }
                    })->sum('quantity');

                return [$product->name => $sum];
            });
            if ($revenueLossPerProduct->isEmpty()) {
                $revenueLossPerProduct = collect(['No Data' => 0]);
            }

            // Inventory status
            $inventoryStatusQuery = Product::query();
            if ($month || $day) {
                // Optionally filter inventory by created_at if needed
            }
            $inventoryStatus = [
                'In Stock' => $inventoryStatusQuery->where('quantity', '>=', 10)->count(),
                'Low Stock' => Product::whereBetween('quantity', [6, 9])->count(),
                'Critical' => Product::whereBetween('quantity', [1, 5])->count(),
                'Out of Stock' => Product::where('quantity', '<=', 0)->count(),
            ];

            // Monthly sales trend
            $monthlySalesTrendQuery = PastOrder::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
                ->whereYear('created_at', $year);
            if ($request->filled('brand_id')) {
                $monthlySalesTrendQuery->where('brand_id', $request->brand_id);
            }
            if ($request->filled('branch_id')) {
                $monthlySalesTrendQuery->where('branch_id', $request->branch_id);
            }
            if ($month) {
                $monthlySalesTrendQuery->whereMonth('created_at', $month);
            }
            if ($day) {
                $monthlySalesTrendQuery->whereDay('created_at', $day);
            }
            $monthlySalesTrend = $monthlySalesTrendQuery
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->mapWithKeys(function ($row) {
                    return [date('M', mktime(0, 0, 0, $row->month, 1)) => $row->total];
                });
            if ($monthlySalesTrend->isEmpty()) {
                $monthlySalesTrend = collect(['No Data' => 0]);
            }

            // Compute profit vs expenses in the same filtered period dynamically from past_order_items and product original_price
            $profitTotal = PastOrderItem::query()
                ->whereIn('past_order_id', $pastOrderIds)
                ->join('products', 'past_order_items.product_id', '=', 'products.id')
                ->selectRaw('COALESCE(SUM((past_order_items.price - COALESCE(products.original_price, 0)) * past_order_items.quantity),0) as profit_total')
                ->value('profit_total');
            $expenseQuery = Expense::query();
            // Map PastOrder created_at filters to Expense date
            $expenseQuery->whereYear('date', $year);
            if ($month) {
                $expenseQuery->whereMonth('date', $month);
            }
            if ($day) {
                $expenseQuery->whereDay('date', $day);
            }
            if ($request->filled('brand_id')) {
                $expenseQuery->where('brand_id', $request->brand_id);
            }
            if ($request->filled('branch_id')) {
                $expenseQuery->where('branch_id', $request->branch_id);
            }
            $expenseTotal = (float) $expenseQuery->sum('amount');

            // Monthly profit vs expenses trend
            $monthlyProfitTrend = PastOrder::selectRaw('MONTH(past_orders.created_at) as month, COALESCE(SUM((poi.price - COALESCE(p.original_price,0)) * poi.quantity),0) as profit_total')
                ->join('past_order_items as poi', 'poi.past_order_id', '=', 'past_orders.id')
                ->join('products as p', 'p.id', '=', 'poi.product_id')
                ->whereYear('past_orders.created_at', $year)
                ->when($request->filled('brand_id'), fn ($q) => $q->where('past_orders.brand_id', $request->brand_id))
                ->when($request->filled('branch_id'), fn ($q) => $q->where('past_orders.branch_id', $request->branch_id))
                ->when($month, fn ($q) => $q->whereMonth('past_orders.created_at', $month))
                ->when($day, fn ($q) => $q->whereDay('past_orders.created_at', $day))
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->mapWithKeys(function ($row) {
                    return [date('M', mktime(0, 0, 0, $row->month, 1)) => (float) $row->profit_total];
                });

            $monthlyExpenseTrend = Expense::selectRaw('MONTH(date) as month, SUM(amount) as total')
                ->whereYear('date', $year)
                ->when($request->filled('brand_id'), fn ($q) => $q->where('brand_id', $request->brand_id))
                ->when($request->filled('branch_id'), fn ($q) => $q->where('branch_id', $request->branch_id))
                ->when($month, fn ($q) => $q->whereMonth('date', $month))
                ->when($day, fn ($q) => $q->whereDay('date', $day))
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->mapWithKeys(function ($row) {
                    return [date('M', mktime(0, 0, 0, $row->month, 1)) => (float) $row->total];
                });

            return response()->json([
                'success' => true,
                'product_sales' => $productSales,
                'graph_data' => $graphData,
                'rankings' => [
                    'top_10_products' => $top10Products,
                    'bottom_10_products' => $bottom10Products,
                    'most_orders_this_month' => $mostOrdersThisMonth,
                    'total_sales_this_month' => $totalSalesThisMonth,
                    'total_orders_this_year' => $totalOrdersThisYear,
                    'total_sales_this_year' => $totalSalesThisYear,
                ],
                'filters' => [
                    'brands' => Brand::all(),
                    'branches' => Branch::all(),
                    'products' => Product::all(['id', 'name']),
                ],
                'revenue_loss' => $revenueLoss,
                'average_order_value' => $averageOrderValue,
                'sales_per_store' => $salesPerStore,
                'sales_per_brand' => $salesPerBrand,
                'orders_per_store' => $ordersPerStore,
                'orders_per_brand' => $ordersPerBrand,
                'revenue_loss_per_product' => $revenueLossPerProduct,
                'inventory_status' => $inventoryStatus,
                'monthly_sales_trend' => $monthlySalesTrend,
                'profit_vs_expenses' => [
                    'profit_total' => $profitTotal,
                    'expense_total' => $expenseTotal,
                    'monthly_profit' => $monthlyProfitTrend,
                    'monthly_expense' => $monthlyExpenseTrend,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve analytics: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function showView()
    {
        if (! Auth::check()) {
            return redirect()->route('Login');
        }

        return view('owner.dashboard');
    }
}
