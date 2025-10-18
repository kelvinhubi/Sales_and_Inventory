<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PastOrder;
use App\Models\PastOrderItem;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function showView(): View
    {
        if (! Auth::check()) {
            return redirect()->route('Login');
        }

        return view('owner.order');
    }
    /**
     * Get all orders with filtering, searching, and sorting
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Order::with(['brand', 'branch', 'items'])
                ->where('user_id', Auth::id()); // Filter by authenticated user

            // Search functionality
            if ($request->has('search') && ! empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereHas('brand', function ($brandQuery) use ($searchTerm) {
                        $brandQuery->where('name', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhereHas('branch', function ($branchQuery) use ($searchTerm) {
                        $branchQuery->where('name', 'LIKE', "%{$searchTerm}%");
                    })
                    ->orWhere('notes', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Brand filter
            if ($request->has('brand_id') && ! empty($request->brand_id)) {
                $query->where('brand_id', $request->brand_id);
            }

            // Branch filter
            if ($request->has('branch_id') && ! empty($request->branch_id)) {
                $query->where('branch_id', $request->branch_id);
            }

            // Sorting
            $sortBy = $request->get('sort', 'date');
            switch ($sortBy) {
                case 'total':
                    $query->orderBy('total_amount', 'desc');

                    break;
                case 'brand':
                    $query->join('brands', 'orders.brand_id', '=', 'brands.id')
                          ->orderBy('brands.name', 'asc')
                          ->select('orders.*');

                    break;
                case 'date':
                default:
                    $query->orderBy('created_at', 'desc');

                    break;
            }

            $orders = $query->get();

            // Transform the data to match frontend expectations
            $transformedOrders = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'brand_id' => $order->brand_id,
                    'branch_id' => $order->branch_id,
                    'brand_name' => $order->brand->name ?? 'Unknown Brand',
                    'branch_name' => $order->branch->name ?? 'Unknown Branch',
                    'total_amount' => number_format($order->total_amount, 2, '.', ''),
                    'notes' => $order->notes,
                    'created_at' => $order->created_at->toISOString(),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name, // Use 'name' field
                            'quantity' => $item->quantity,
                            'price' => number_format($item->price, 2, '.', ''), // Use 'price' field
                            'product_id' => $item->product_id,
                        ];
                    }),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedOrders,
                'message' => 'Orders retrieved successfully',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve orders: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new order
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'branch_id' => 'required|exists:branches,id',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.product_id' => 'required|exists:products,id',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // Create the order
            $order = Order::create([
                'user_id' => Auth::id(), // Save authenticated user's ID
                'brand_id' => $validatedData['brand_id'],
                'branch_id' => $validatedData['branch_id'],
                'total_amount' => $validatedData['total_amount'],
                'notes' => $validatedData['notes'] ?? null,
            ]);

            // Create order items - using correct field names
            foreach ($validatedData['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'name' => $item['name'], // Use 'name' instead of 'product_name'
                    'quantity' => $item['quantity'],
                    'price' => $item['price'], // Use 'price' instead of 'unit_price'
                ]);
            }

            DB::commit();

            // Load relationships for response
            $order->load(['brand', 'branch', 'items']);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $order->id,
                    'brand_id' => $order->brand_id,
                    'branch_id' => $order->branch_id,
                    'brand_name' => $order->brand->name,
                    'branch_name' => $order->branch->name,
                    'total_amount' => number_format($order->total_amount, 2, '.', ''),
                    'notes' => $order->notes,
                    'created_at' => $order->created_at->toISOString(),
                ],
                'message' => 'Order created successfully',
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show a specific order
     */
    public function show($id): JsonResponse
    {
        try {
            $order = Order::with(['brand', 'branch', 'items'])
                ->where('id', $id)
                ->where('user_id', Auth::id()) // Ensure user owns this order
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $order->id,
                    'brand_id' => $order->brand_id,
                    'branch_id' => $order->branch_id,
                    'brand_name' => $order->brand->name,
                    'branch_name' => $order->branch->name,
                    'total_amount' => number_format($order->total_amount, 2, '.', ''),
                    'notes' => $order->notes,
                    'created_at' => $order->created_at->toISOString(),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name, // Use 'name' field
                            'quantity' => $item->quantity,
                            'price' => number_format($item->price, 2, '.', ''), // Use 'price' field
                            'product_id' => $item->product_id,
                        ];
                    }),
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }
    }

    /**
     * Update an existing order
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validatedData = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'branch_id' => 'required|exists:branches,id',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.product_id' => 'required|exists:products,id',
            'total_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $order = Order::where('id', $id)
                ->where('user_id', Auth::id()) // Ensure user owns this order
                ->firstOrFail();

            // Update order details
            $order->update([
                'brand_id' => $validatedData['brand_id'],
                'branch_id' => $validatedData['branch_id'],
                'total_amount' => $validatedData['total_amount'],
                'notes' => $validatedData['notes'] ?? null,
            ]);

            // Delete existing order items
            $order->items()->delete();

            // Create new order items - using correct field names
            foreach ($validatedData['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'name' => $item['name'], // Use 'name' instead of 'product_name'
                    'quantity' => $item['quantity'],
                    'price' => $item['price'], // Use 'price' instead of 'unit_price'
                ]);
            }

            DB::commit();

            // Load relationships for response
            $order->load(['brand', 'branch', 'items']);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $order->id,
                    'brand_id' => $order->brand_id,
                    'branch_id' => $order->branch_id,
                    'brand_name' => $order->brand->name,
                    'branch_name' => $order->branch->name,
                    'total_amount' => number_format($order->total_amount, 2, '.', ''),
                    'notes' => $order->notes,
                    'created_at' => $order->created_at->toISOString(),
                ],
                'message' => 'Order updated successfully',
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete an order
     */
    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $order = Order::where('id', $id)
                ->where('user_id', Auth::id()) // Ensure user owns this order
                ->firstOrFail();

            // Delete order items first (due to foreign key constraints)
            $order->items()->delete();

            // Delete the order
            $order->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully',
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate final order summary grouped by brands and branches
     */
    public function finalSummary(): JsonResponse
    {
        try {
            // Check if any orders exist for the authenticated user
            $orderCount = Order::where('user_id', Auth::id())->count();

            if ($orderCount === 0) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'brands' => [],
                        'total' => 0,
                        'message' => 'No orders found',
                    ],
                ]);
            }

            // Get all orders with relationships for the authenticated user
            $orders = Order::with(['brand', 'branch', 'items'])
                ->where('user_id', Auth::id())
                ->get();

            // Group orders by brand, then by branch
            $groupedData = [];
            $grandTotal = 0;

            foreach ($orders as $order) {
                $brandId = $order->brand_id;
                $branchId = $order->branch_id;
                $brandName = $order->brand->name ?? 'Unknown Brand';
                $branchName = $order->branch->name ?? 'Unknown Branch';

                // Initialize brand if not exists
                if (! isset($groupedData[$brandId])) {
                    $groupedData[$brandId] = [
                        'id' => $brandId,
                        'name' => $brandName,
                        'branches' => [],
                    ];
                }

                // Initialize branch if not exists
                if (! isset($groupedData[$brandId]['branches'][$branchId])) {
                    $groupedData[$brandId]['branches'][$branchId] = [
                        'id' => $branchId,
                        'name' => $branchName,
                        'orders' => [],
                    ];
                }

                // Add order to branch
                $orderData = [
                    'id' => $order->id,
                    'total_amount' => number_format($order->total_amount, 2, '.', ''),
                    'created_at' => $order->created_at->toISOString(),
                    'notes' => $order->notes,
                    'items' => $order->items->map(function ($item) {
                        // Get current inventory stock for this product
                        $product = Product::find($item->product_id);
                        $currentStock = $product ? $product->quantity : 0;
                        $afterDeduction = max(0, $currentStock - $item->quantity);
                        
                        return [
                            'product_id' => $item->product_id,
                            'name' => $item->name, // Use 'name' field
                            'quantity' => $item->quantity,
                            'price' => number_format($item->price, 2, '.', ''), // Use 'price' field
                            'subtotal' => number_format($item->quantity * $item->price, 2, '.', ''),
                            'current_stock' => $currentStock,
                            'after_deduction' => $afterDeduction,
                            'deduction_amount' => $item->quantity,
                        ];
                    }),
                ];

                $groupedData[$brandId]['branches'][$branchId]['orders'][] = $orderData;
                $grandTotal += $order->total_amount;
            }

            // Convert associative arrays to indexed arrays for frontend
            $finalData = [];
            foreach ($groupedData as $brand) {
                $brandBranches = [];
                foreach ($brand['branches'] as $branch) {
                    $brandBranches[] = $branch;
                }
                $brand['branches'] = $brandBranches;
                $finalData[] = $brand;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'brands' => $finalData,
                    'total' => number_format($grandTotal, 2, '.', ''),
                    'order_count' => $orderCount,
                    'generated_at' => now()->toISOString(),
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate final summary: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get order statistics for dashboard
     */
    public function statistics(): JsonResponse
    {
        try {
            $totalOrders = Order::count();
            $totalValue = Order::sum('total_amount');
            $uniqueBranches = Order::distinct('branch_id')->count();
            $avgOrderValue = $totalOrders > 0 ? $totalValue / $totalOrders : 0;

            // Recent orders (last 7 days)
            $recentOrders = Order::where('created_at', '>=', now()->subDays(7))->count();

            // Top selling products
            $topProducts = OrderItem::select('name', DB::raw('SUM(quantity) as total_quantity'))
                ->groupBy('name')
                ->orderBy('total_quantity', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_orders' => $totalOrders,
                    'total_value' => number_format($totalValue, 2, '.', ''),
                    'unique_branches' => $uniqueBranches,
                    'avg_order_value' => number_format($avgOrderValue, 2, '.', ''),
                    'recent_orders' => $recentOrders,
                    'top_products' => $topProducts,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deductInventory(Request $request): JsonResponse
    {
        try {
            // Manually get the JSON data
            $requestData = $request->json()->all();

            // Check if the JSON has the expected structure
            if (! isset($requestData['order_id']['brands'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid JSON structure. "brands" key not found.',
                ], 400);
            }

            // Begin a database transaction
            DB::beginTransaction();

            // Loop through each brand
            foreach ($requestData['order_id']['brands'] as $brandData) {
                // Loop through each branch within the brand
                foreach ($brandData['branches'] as $branchData) {
                    // Loop through each order within the branch
                    foreach ($branchData['orders'] as $orderData) {
                        $orderId = $orderData['id'];

                        // Find the order in the database
                        $order = Order::findOrFail($orderId);

                        // Generate DR number
                        $drNumber = $this->generateDRNumber();

                        // Transfer the order to the past_orders table
                        $pastOrder = PastOrder::create([
                            'brand_id' => $order->brand_id,
                            'branch_id' => $order->branch_id,
                            'total_amount' => $order->total_amount,
                            'dr_number' => $drNumber,
                            'created_at' => $order->created_at,
                            'updated_at' => $order->updated_at,
                        ]);

                        // Transfer the order items to the past_order_items table
                        foreach ($order->items as $item) {
                            $product = Product::findOrFail($item->product_id);
                            PastOrderItem::create([
                                'past_order_id' => $pastOrder->id,
                                'product_id' => $item->product_id,
                                'quantity' => $item->quantity,
                                'price' => $item->price,
                            ]);

                            // Decrement product inventory
                            $product->decrement('quantity', $item->quantity);

                            // Set quantity to zero instead of deleting product
                            if ($product->quantity <= 0) {
                                $product->quantity = 0;
                                $product->save();
                            }
                        }
                        // No profit persistence as requested

                        // Delete the original order
                        $order->delete();
                    }
                }
            }

            DB::commit(); // Commit the transaction

            return response()->json(['success' => true, 'message' => 'Inventory deducted and orders archived successfully.'], 200);

        } catch (\Exception $e) {
            DB::rollBack(); // Roll back in case of an error

            return response()->json([
                'success' => false,
                'message' => 'Inventory deduction failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Generate a unique DR (Delivery Receipt) number
     */
    private function generateDRNumber(): string
    {
        // Get current date in YYYYMMDD format
        $datePrefix = now()->format('Ymd');

        // Get the count of past orders created today + 1
        $todayCount = PastOrder::whereDate('created_at', now()->toDateString())->count() + 1;

        // Generate DR number in format: DR-YYYYMMDD-XXXX (e.g., DR-20250117-0001)
        $drNumber = sprintf('DR-%s-%04d', $datePrefix, $todayCount);

        // Ensure uniqueness by checking if it already exists
        while (PastOrder::where('dr_number', $drNumber)->exists()) {
            $todayCount++;
            $drNumber = sprintf('DR-%s-%04d', $datePrefix, $todayCount);
        }

        return $drNumber;
    }
}
