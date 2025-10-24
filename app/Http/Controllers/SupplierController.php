<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Traits\LogsActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupplierController extends Controller
{
    use LogsActivity;
    public function showView(): View
    {
        if (! Auth::check()) {
            return redirect()->route('Login');
        }

        return view('owner.suppliers');
    }
    public function showView2(): View
    {
        if (! Auth::check()) {
            return redirect()->route('Login');
        }

        return view('manager.suppliers');
    }

    /**
     * Get all suppliers
     */
    public function index(Request $request): JsonResponse
    {
        $query = Supplier::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'name':
                    $query->orderBy('name');
                    break;
                case 'company':
                    $query->orderBy('company');
                    break;
                case 'recent':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('name');
            }
        } else {
            $query->orderBy('name');
        }

        $suppliers = $query->get();

        return response()->json($suppliers);
    }

    /**
     * Store a new supplier
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $supplier = Supplier::create($validated);

        // Log supplier creation
        self::logActivity(
            'create',
            'suppliers',
            "Created supplier: {$supplier->name}",
            [
                'supplier_id' => $supplier->id,
                'name' => $supplier->name,
                'company' => $supplier->company
            ],
            'medium'
        );

        return response()->json($supplier, 201);
    }

    /**
     * Get a specific supplier
     */
    public function show(Supplier $supplier): JsonResponse
    {
        return response()->json($supplier);
    }

    /**
     * Update a supplier
     */
    public function update(Request $request, Supplier $supplier): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $oldName = $supplier->name;
        $supplier->update($validated);

        // Log supplier update
        self::logActivity(
            'update',
            'suppliers',
            "Updated supplier: {$oldName}" . ($oldName !== $supplier->name ? " to {$supplier->name}" : ""),
            [
                'supplier_id' => $supplier->id,
                'old_name' => $oldName,
                'new_name' => $supplier->name
            ],
            'medium'
        );

        return response()->json($supplier);
    }

    /**
     * Delete a supplier
     */
    public function destroy(Supplier $supplier): JsonResponse
    {
        $supplierName = $supplier->name;
        $supplierId = $supplier->id;
        
        $supplier->delete();

        // Log supplier deletion (high severity)
        self::logActivity(
            'delete',
            'suppliers',
            "Deleted supplier: {$supplierName}",
            [
                'supplier_id' => $supplierId,
                'supplier_name' => $supplierName
            ],
            'high'
        );

        return response()->json(['message' => 'Supplier deleted successfully']);
    }
}
