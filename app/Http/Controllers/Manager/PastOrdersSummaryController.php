<?php

namespace App\Http\Controllers\Manager;

use App\Exports\PastOrdersSummaryReport;
use App\Http\Controllers\Controller;
use App\Models\PastOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class PastOrdersSummaryController extends Controller
{
    public function exportSummaryReport(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('homepage');
        }

        try {
            $validation = [
                'selected_orders' => 'required|string',
                'start_date' => 'nullable|date|before_or_equal:today',
                'end_date' => 'nullable|date|before_or_equal:today',
                'search' => 'nullable|string',
                'branch_search' => 'nullable|string',
                'brand_search' => 'nullable|string',
            ];

            // Only validate end_date against start_date if start_date is provided
            if ($request->filled('start_date')) {
                $validation['end_date'] .= '|after_or_equal:start_date';
            }

            $request->validate($validation);

            // Get selected order IDs
            $selectedIds = explode(',', $request->selected_orders);
            $selectedIds = array_filter($selectedIds); // Remove empty values

            if (empty($selectedIds)) {
                return redirect()->back()->with('error', 'No orders selected for summary report.');
            }

            // Build the query using the EXACT same logic as the main PastOrderController
            $query = PastOrder::with(['items.product', 'brand', 'branch']);

            // Apply the same filters as the main controller
            $search = $request->input('search');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $branchSearch = $request->input('branch_search');
            $brandSearch = $request->input('brand_search');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%$search%")
                      ->orWhereHas('brand', function ($q2) use ($search) {
                          $q2->where('name', 'like', "%$search%");
                      })
                      ->orWhereHas('branch', function ($q2) use ($search) {
                          $q2->where('name', 'like', "%$search%");
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

            // Now filter to only the selected orders that match the criteria
            $query->whereIn('id', $selectedIds);

            $pastOrders = $query->get();

            // Store filter criteria for the report
            $filterCriteria = [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'search' => $search,
                'branch_search' => $branchSearch,
                'brand_search' => $brandSearch,
                'selected_count' => count($selectedIds),
                'filtered_count' => $pastOrders->count(),
            ];

            // Generate filename with current date
            $filename = 'past_orders_summary_report_' . now()->format('Y_m_d_His') . '.xlsx';

            // Clear any output buffers to prevent HTML contamination
            if (ob_get_contents()) {
                ob_clean();
            }

            // Export Excel file
            $export = new PastOrdersSummaryReport($pastOrders, $filterCriteria);

            $response = Excel::download($export, $filename);

            // Add headers to prevent caching issues
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');

            return $response;

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating summary report: ' . $e->getMessage());
        }
    }
}
