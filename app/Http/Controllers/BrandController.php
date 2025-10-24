<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Traits\LogsActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BrandController extends Controller
{
    use LogsActivity;
    public function showView(): View
    {
        if (! Auth::check()) {
            return redirect()->route('Login');
        }

        return view('owner.brand');
    }
    /**
     * Get all brands with their branches
     */
    public function index(Request $request): JsonResponse
    {
        $query = Brand::with('branches');

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
                case 'branches':
                    $query->withCount('branches')->orderBy('branches_count', 'desc');

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

        $brands = $query->get();

        return response()->json($brands);
    }

    /**
     * Store a new brand
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands',
            'description' => 'nullable|string',
            'standard_items' => 'nullable|array',
        ]);

        $brand = Brand::create($validated);
        $brand->load('branches');

        // Log brand creation
        self::logActivity(
            'create',
            'brands',
            "Created brand: {$brand->name}",
            [
                'brand_id' => $brand->id,
                'name' => $brand->name
            ],
            'medium'
        );

        return response()->json($brand, 201);
    }

    /**
     * Get a specific brand with branches
     */
    public function show(Brand $brand): JsonResponse
    {
        $brand->load('branches');

        return response()->json($brand);
    }

    /**
     * Update a brand
     */
    public function update(Request $request, Brand $brand): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string',
            'standard_items' => 'nullable|array',
        ]);

        $oldName = $brand->name;
        $brand->update($validated);
        $brand->load('branches');

        // Log brand update
        self::logActivity(
            'update',
            'brands',
            "Updated brand: {$oldName}" . ($oldName !== $brand->name ? " to {$brand->name}" : ""),
            [
                'brand_id' => $brand->id,
                'old_name' => $oldName,
                'new_name' => $brand->name
            ],
            'medium'
        );

        return response()->json($brand);
    }

    /**
     * Delete a brand and all its branches
     */
    public function destroy(Brand $brand): JsonResponse
    {
        $brandName = $brand->name;
        $brandId = $brand->id;
        $branchesCount = $brand->branches()->count();
        
        $brand->delete(); // This will also delete branches due to cascade

        // Log brand deletion (high severity due to cascade delete)
        self::logActivity(
            'delete',
            'brands',
            "Deleted brand: {$brandName} (including {$branchesCount} branches)",
            [
                'brand_id' => $brandId,
                'brand_name' => $brandName,
                'branches_deleted' => $branchesCount
            ],
            'high'
        );

        return response()->json([
            'message' => 'Brand and all its branches deleted successfully',
        ]);
    }
}
