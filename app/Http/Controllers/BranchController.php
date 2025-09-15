<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BranchController extends Controller
{
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
            'status' => 'sometimes|string|in:active,inactive'
        ]);

        $branch = $brand->branches()->create($validated);

        return response()->json($branch, 201);
    }

    /**
     * Get a specific branch
     */
    public function show(Brand $brand, Branch $branch): JsonResponse
    {
        if (!Auth::check()) {
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
            'status' => 'sometimes|string|in:active,inactive'
        ]);

        $branch->update($validated);

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

        $branch->delete();

        return response()->json([
            'message' => 'Branch deleted successfully'
        ]);
    }

    public function allBranches(): JsonResponse
    {
        return response()->json(Branch::all());
    }
}