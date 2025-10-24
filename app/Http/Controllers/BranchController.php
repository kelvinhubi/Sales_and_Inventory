<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Brand;
use App\Traits\LogsActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    use LogsActivity;
    /**
     * Get all branches for a specific brand
     */
    public function index(Request $request, Brand $brand): JsonResponse
    {
        $query = $brand->branches();

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'name':
                    $query->orderBy('name');

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

        $branches = $query->get();

        return response()->json($branches);
    }

    /**
     * Store a new branch for a brand
     */
    public function store(Request $request, Brand $brand): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'status' => 'sometimes|string|in:active,inactive',
        ]);

        $branch = $brand->branches()->create($validated);

        // Log branch creation
        self::logActivity(
            'create',
            'branches',
            "Created branch: {$branch->name} for brand: {$brand->name}",
            [
                'branch_id' => $branch->id,
                'branch_name' => $branch->name,
                'brand_id' => $brand->id,
                'brand_name' => $brand->name
            ],
            'medium'
        );

        return response()->json($branch, 201);
    }

    /**
     * Get a specific branch
     */
    public function show(Brand $brand, Branch $branch): JsonResponse
    {
        if (! Auth::check()) {
            return redirect()->route('Login');
        }
        // Ensure the branch belongs to the brand
        if ($branch->brand_id !== $brand->id) {
            return response()->json(['error' => 'Branch not found for this brand'], 404);
        }

        return response()->json($branch);
    }

    /**
     * Update a branch
     */
    public function update(Request $request, Brand $brand, Branch $branch): JsonResponse
    {
        // Ensure the branch belongs to the brand
        if ($branch->brand_id !== $brand->id) {
            return response()->json(['error' => 'Branch not found for this brand'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'status' => 'sometimes|string|in:active,inactive',
        ]);

        $oldName = $branch->name;
        $branch->update($validated);

        // Log branch update
        self::logActivity(
            'update',
            'branches',
            "Updated branch: {$oldName}" . ($oldName !== $branch->name ? " to {$branch->name}" : "") . " for brand: {$brand->name}",
            [
                'branch_id' => $branch->id,
                'old_name' => $oldName,
                'new_name' => $branch->name,
                'brand_name' => $brand->name
            ],
            'medium'
        );

        return response()->json($branch);
    }

    /**
     * Delete a branch
     */
    public function destroy(Brand $brand, Branch $branch): JsonResponse
    {
        // Ensure the branch belongs to the brand
        if ($branch->brand_id !== $brand->id) {
            return response()->json(['error' => 'Branch not found for this brand'], 404);
        }

        $branchName = $branch->name;
        $branchId = $branch->id;
        $brandName = $brand->name;
        
        $branch->delete();

        // Log branch deletion (high severity)
        self::logActivity(
            'delete',
            'branches',
            "Deleted branch: {$branchName} from brand: {$brandName}",
            [
                'branch_id' => $branchId,
                'branch_name' => $branchName,
                'brand_name' => $brandName
            ],
            'high'
        );

        return response()->json([
            'message' => 'Branch deleted successfully',
        ]);
    }

    public function allBranches(): JsonResponse
    {
        return response()->json(Branch::all());
    }
}
