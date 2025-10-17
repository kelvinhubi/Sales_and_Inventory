<?php

namespace App\Http\Controllers\Manager;

use App\Exports\PastOrdersExport;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\PastOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PastOrderController extends Controller
{
    public function index()
    {
        if (! Auth::check()) {
            return redirect()->route('homepage');
        }

        $this->authorize('viewAny', PastOrder::class);

        // Validate date inputs to prevent future dates
        $validation = [
            'start_date' => 'nullable|date|before_or_equal:today',
            'end_date' => 'nullable|date|before_or_equal:today',
        ];

        // Only validate end_date against start_date if start_date is provided
        if (request()->filled('start_date')) {
            $validation['end_date'] .= '|after_or_equal:start_date';
        }

        request()->validate($validation);

        $query = PastOrder::with(['items.product', 'brand', 'branch']);

        // Filters
        $search = request()->input('search');
        $startDate = request()->input('start_date');
        $endDate = request()->input('end_date');
        $branchSearch = request()->input('branch_search');
        $brandSearch = request()->input('brand_search');
        $sortDirection = request()->input('sort_direction', 'desc');
        $sortBy = 'created_at';

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('dr_number', 'like', "%{$search}%")
                  ->orWhere('total_amount', 'like', "%{$search}%")
                  ->orWhereHas('items', function ($itemQuery) use ($search) {
                      $itemQuery->whereHas('product', function ($productQuery) use ($search) {
                          $productQuery->where('product_name', 'like', "%{$search}%");
                      });
                  });
            });
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if ($branchSearch) {
            $query->whereHas('branch', function ($q) use ($branchSearch) {
                $q->where('branch_name', 'like', "%{$branchSearch}%");
            });
        }

        if ($brandSearch) {
            $query->whereHas('brand', function ($q) use ($brandSearch) {
                $q->where('brand_name', 'like', "%{$brandSearch}%");
            });
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortDirection);

        $pastOrders = $query->paginate(10);
        $branches = Branch::all();
        $brands = Brand::all();

        // Calculate statistics
        $totalOrders = $pastOrders->total();
        $totalRevenue = PastOrder::sum('total_amount');
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return view('manager.past-orders', compact('pastOrders', 'branches', 'brands', 'totalOrders', 'totalRevenue', 'avgOrderValue'));
    }

    public function show(PastOrder $pastOrder)
    {
        if (! Auth::check()) {
            return redirect()->route('homepage');
        }

        $this->authorize('view', $pastOrder);

        $pastOrder->load(['items.product', 'brand', 'branch']);

        return view('manager.past-orders.show', compact('pastOrder'));
    }

    public function exportSelected(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('homepage');
        }

        $this->authorize('viewAny', PastOrder::class);

        // Validate date inputs
        $validation = [
            'start_date' => 'nullable|date|before_or_equal:today',
            'end_date' => 'nullable|date|before_or_equal:today',
        ];

        // Only validate end_date against start_date if start_date is provided
        if ($request->filled('start_date')) {
            $validation['end_date'] .= '|after_or_equal:start_date';
        }

        $request->validate($validation);

        // Get selected order IDs from request
        $selectedOrderIds = explode(',', $request->input('selected_orders'));

        // Debug: Log what we received
        \Log::info('Export Selected - Received data:', [
            'selected_orders' => $request->input('selected_orders'),
            'parsed_ids' => $selectedOrderIds,
            'count' => count($selectedOrderIds),
        ]);

        // Validate that we have selections
        if (empty($selectedOrderIds) || (count($selectedOrderIds) == 1 && empty($selectedOrderIds[0]))) {
            return redirect()->back()->with('error', 'No orders selected for export.');
        }

        // Filter out empty values and ensure they're integers
        $selectedOrderIds = array_filter(array_map('intval', $selectedOrderIds));

        if (empty($selectedOrderIds)) {
            return redirect()->back()->with('error', 'Invalid order selection.');
        }

        // Build query for selected orders
        $query = PastOrder::with(['items.product', 'brand', 'branch'])
                          ->whereIn('id', $selectedOrderIds);

        // Apply additional filters from the form
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $search = $request->input('search');
        $branchSearch = $request->input('branch_search');
        $brandSearch = $request->input('brand_search');

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('dr_number', 'like', "%{$search}%")
                  ->orWhere('total_amount', 'like', "%{$search}%")
                  ->orWhereHas('items', function ($itemQuery) use ($search) {
                      $itemQuery->whereHas('product', function ($productQuery) use ($search) {
                          $productQuery->where('product_name', 'like', "%{$search}%");
                      });
                  });
            });
        }

        if ($branchSearch) {
            $query->whereHas('branch', function ($q) use ($branchSearch) {
                $q->where('branch_name', 'like', "%{$branchSearch}%");
            });
        }

        if ($brandSearch) {
            $query->whereHas('brand', function ($q) use ($brandSearch) {
                $q->where('brand_name', 'like', "%{$brandSearch}%");
            });
        }

        $pastOrders = $query->get();

        if ($pastOrders->isEmpty()) {
            return redirect()->back()->with('error', 'No orders found matching the criteria.');
        }

        // Debug: Log what we're exporting
        \Log::info('Exporting orders:', [
            'count' => $pastOrders->count(),
            'orders' => $pastOrders->pluck('id')->toArray(),
        ]);

        $fileName = 'selected_past_orders_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new PastOrdersExport($pastOrders), $fileName);
    }

    public function destroy(PastOrder $pastOrder)
    {
        if (! Auth::check()) {
            return redirect()->route('homepage');
        }

        $this->authorize('delete', $pastOrder);

        try {
            // Delete related order items first
            $pastOrder->items()->delete();

            // Delete the past order
            $pastOrder->delete();

            return redirect()->route('manager.past-orders.index')->with('success', 'Past order deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete past order: ' . $e->getMessage());

            return redirect()->route('manager.past-orders.index')->with('error', 'Failed to delete past order.');
        }
    }

    public function deleteSelected(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('homepage');
        }

        $this->authorize('deleteAny', PastOrder::class);

        $selectedOrderIds = explode(',', $request->input('selected_orders'));

        // Filter out empty values and ensure they're integers
        $selectedOrderIds = array_filter(array_map('intval', $selectedOrderIds));

        if (empty($selectedOrderIds)) {
            return redirect()->back()->with('error', 'No orders selected for deletion.');
        }

        try {
            // Delete related order items first
            DB::table('past_order_items')->whereIn('past_order_id', $selectedOrderIds)->delete();

            // Delete the past orders
            $deletedCount = PastOrder::whereIn('id', $selectedOrderIds)->delete();

            return redirect()->route('manager.past-orders.index')->with('success', "Successfully deleted {$deletedCount} past order(s).");
        } catch (\Exception $e) {
            Log::error('Failed to delete selected past orders: ' . $e->getMessage());

            return redirect()->route('manager.past-orders.index')->with('error', 'Failed to delete selected past orders.');
        }
    }

    public function testDelete(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('homepage');
        }

        $selectedOrderIds = $request->input('selected_orders');

        // Debug log
        Log::info('Test Delete received:', [
            'selected_orders' => $selectedOrderIds,
            'type' => gettype($selectedOrderIds),
        ]);

        return response()->json([
            'received' => $selectedOrderIds,
            'type' => gettype($selectedOrderIds),
            'exploded' => explode(',', $selectedOrderIds),
        ]);
    }
}
