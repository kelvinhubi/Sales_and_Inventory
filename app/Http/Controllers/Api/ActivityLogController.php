<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Get all activity logs with filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

            // Filter by user (for owner viewing all logs)
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by action type
            if ($request->filled('action_type')) {
                $query->where('action_type', $request->action_type);
            }

            // Filter by module
            if ($request->filled('module')) {
                $query->where('module', $request->module);
            }

            // Filter by severity
            if ($request->filled('severity')) {
                $query->where('severity', $request->severity);
            }

            // Filter by date range
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('user_name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('ip_address', 'LIKE', "%{$searchTerm}%");
                });
            }

            // Pagination
            $perPage = $request->get('per_page', 50);
            $logs = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $logs,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve activity logs: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $userId = $request->get('user_id', Auth::id());

            // If user is manager, only show their logs
            $query = ActivityLog::query();
            if (Auth::user()->Role === 'manager') {
                $query->where('user_id', Auth::id());
            } elseif ($userId) {
                $query->where('user_id', $userId);
            }

            $stats = [
                'total_activities' => $query->count(),
                'today_activities' => (clone $query)->whereDate('created_at', today())->count(),
                'this_week_activities' => (clone $query)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'critical_activities' => (clone $query)->where('severity', 'critical')->count(),
                'high_severity' => (clone $query)->where('severity', 'high')->count(),
                'failed_logins' => (clone $query)->where('action_type', 'failed_login')->count(),
                'by_action_type' => (clone $query)->select('action_type', \DB::raw('count(*) as count'))
                    ->groupBy('action_type')
                    ->get(),
                'by_module' => (clone $query)->select('module', \DB::raw('count(*) as count'))
                    ->groupBy('module')
                    ->get(),
                'recent_activities' => (clone $query)->orderBy('created_at', 'desc')->limit(10)->get(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's own activity logs
     */
    public function myLogs(Request $request): JsonResponse
    {
        try {
            $query = ActivityLog::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc');

            // Filter by action type
            if ($request->filled('action_type')) {
                $query->where('action_type', $request->action_type);
            }

            // Filter by date range
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            $perPage = $request->get('per_page', 20);
            $logs = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $logs,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve your activity logs: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export logs to CSV
     */
    public function export(Request $request)
    {
        try {
            $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

            // Apply same filters as index
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            if ($request->filled('action_type')) {
                $query->where('action_type', $request->action_type);
            }
            if ($request->filled('module')) {
                $query->where('module', $request->module);
            }
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }

            $logs = $query->get();

            $filename = 'activity_logs_' . date('Y-m-d_His') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($logs) {
                $file = fopen('php://output', 'w');
                
                // Header row
                fputcsv($file, [
                    'ID', 'Date/Time', 'User', 'Role', 'Action', 'Module', 
                    'Description', 'IP Address', 'Severity'
                ]);

                // Data rows
                foreach ($logs as $log) {
                    fputcsv($file, [
                        $log->id,
                        $log->created_at->format('Y-m-d H:i:s'),
                        $log->user_name,
                        $log->user_role,
                        $log->action_type,
                        $log->module,
                        $log->description,
                        $log->ip_address,
                        $log->severity,
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export logs: ' . $e->getMessage(),
            ], 500);
        }
    }
}
