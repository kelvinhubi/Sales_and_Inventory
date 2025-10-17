<?php

namespace App\Http\Controllers\Owner;

use App\Exports\PastOrdersExport;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\PastOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                $q->where('id', 'like', "%$search%")
                  ->orWhereHas('brand', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%") ;
                  })
                  ->orWhereHas('branch', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%") ;
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
            $query->where('branch_id', $branchSearch);
        }
        if ($brandSearch) {
            $query->where('brand_id', $brandSearch);
        }
        $query->orderBy($sortBy, $sortDirection);

        $pastOrders = $query->paginate(15);

        // KPI Stats
        $stats = [
            'total_orders' => PastOrder::count(),
            'total_amount' => PastOrder::sum('total_amount'),
            'orders_today' => PastOrder::whereDate('created_at', now()->toDateString())->count(),
            'orders_this_month' => PastOrder::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];

        // Pagination info for blade
        $showingStart = ($pastOrders->currentPage() - 1) * $pastOrders->perPage() + 1;
        $showingEnd = $showingStart + $pastOrders->count() - 1;
        $totalEntries = $pastOrders->total();

        // Get brands and branches for dropdown filters
        $brands = Brand::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        return view('owner.past-orders', compact('pastOrders', 'stats', 'showingStart', 'showingEnd', 'totalEntries', 'brands', 'branches'));
    }

    public function destroy(PastOrder $pastOrder)
    {
        $this->authorize('delete', $pastOrder);

        $pastOrder->delete();

        return redirect()->route('owner.past-orders.index')
                        ->with('success', 'Past order deleted successfully.');
    }

    public function deleteSelected(Request $request)
    {
        Log::info('deleteSelected method called');
        Log::info('Request data:', $request->all());

        // Handle both formats: selected_ids array or selected_orders comma-separated string
        $selectedIds = [];

        if ($request->has('selected_ids') && is_array($request->selected_ids)) {
            $selectedIds = $request->selected_ids;
        } elseif ($request->has('selected_orders')) {
            $selectedIds = explode(',', $request->selected_orders);
            $selectedIds = array_filter($selectedIds); // Remove empty values
        }

        if (empty($selectedIds)) {
            return redirect()->route('owner.past-orders.index')
                ->with('error', 'No orders selected for deletion.');
        }

        $request->merge(['selected_ids' => $selectedIds]);

        $request->validate([
            'selected_ids' => 'required|array',
            'selected_ids.*' => 'exists:past_orders,id',
        ]);

        $deletedCount = 0;

        foreach ($selectedIds as $id) {
            $pastOrder = PastOrder::findOrFail($id);

            // Check authorization
            if (! $this->authorize('delete', $pastOrder)) {
                continue; // Skip if not authorized
            }

            $pastOrder->delete();
            $deletedCount++;
        }

        return redirect()->route('owner.past-orders.index')
            ->with('success', "Successfully deleted {$deletedCount} past orders.");
    }

    public function testDelete()
    {
        return response()->json(['message' => 'Test delete route working']);
    }

    public function show(PastOrder $pastOrder)
    {
        if (! Auth::check()) {
            return redirect()->route('homepage');
        }

        $this->authorize('view', $pastOrder);

        $pastOrder = PastOrder::with(['items.product', 'brand', 'branch'])->findOrFail($pastOrder->id);

        return view('owner.past-orders.show', compact('pastOrder'));
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
        if (empty($selectedOrderIds) || (count($selectedOrderIds) === 1 && empty($selectedOrderIds[0]))) {
            \Log::warning('Export Selected - No orders selected');

            return redirect()->back()->with('error', 'No orders selected for export.');
        }

        // Build query with all filters applied (same logic as index method)
        $query = PastOrder::with(['brand', 'branch', 'items.product']);

        // Apply search filter
        $search = $request->input('search');
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('brand', function ($brandQuery) use ($search) {
                      $brandQuery->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('branch', function ($branchQuery) use ($search) {
                      $branchQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Apply date range filters
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (! empty($startDate)) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if (! empty($endDate)) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // Apply branch filter
        $branchSearch = $request->input('branch_search');
        if (! empty($branchSearch)) {
            $query->whereHas('branch', function ($branchQuery) use ($branchSearch) {
                $branchQuery->where('name', 'LIKE', "%{$branchSearch}%");
            });
        }

        // Apply brand filter
        $brandSearch = $request->input('brand_search');
        if (! empty($brandSearch)) {
            $query->whereHas('brand', function ($brandQuery) use ($brandSearch) {
                $brandQuery->where('name', 'LIKE', "%{$brandSearch}%");
            });
        }

        // Now restrict to only the selected orders that match the filters
        $query->whereIn('id', $selectedOrderIds);

        $pastOrders = $query->get();

        if ($pastOrders->isEmpty()) {
            return redirect()->back()->with('error', 'Selected orders not found or do not match current filter criteria.');
        }

        // Prepare filter criteria for export class
        $filterCriteria = [
            'search' => $search,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'branch_search' => $branchSearch,
            'brand_search' => $brandSearch,
            'selected_count' => count($selectedOrderIds),
            'found_count' => $pastOrders->count(),
        ];

        // Generate filename with order count and timestamp
        $filename = 'past_orders_detailed_' . $pastOrders->count() . 'orders_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        try {
            // Create single Excel file with multiple sheets, passing filter info
            return Excel::download(new PastOrdersExport($pastOrders, $filterCriteria), $filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function printSelected(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('homepage');
        }

        $this->authorize('viewAny', PastOrder::class);

        // Get selected order IDs from query parameters
        $selectedOrderIds = explode(',', $request->query('ids'));

        // Validate that we have selections
        if (empty($selectedOrderIds) || (count($selectedOrderIds) === 1 && empty($selectedOrderIds[0]))) {
            return redirect()->back()->with('error', 'No orders selected for printing.');
        }

        // Get the selected orders
        $pastOrders = PastOrder::with(['brand', 'branch', 'items.product'])
                              ->whereIn('id', $selectedOrderIds)
                              ->get();

        if ($pastOrders->isEmpty()) {
            return redirect()->back()->with('error', 'Selected orders not found.');
        }

        // Calculate total amount
        $totalAmount = $pastOrders->sum('total_amount');
        $totalOrders = $pastOrders->count();

        return view('owner.past-orders.print', compact('pastOrders', 'totalAmount', 'totalOrders'));
    }
}
