<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RejectedGoodController extends Controller
{
    /**
     * Display a listing of rejected goods
     */
    public function index(): JsonResponse
    {
        // Placeholder - will be implemented with DR number dropdown
        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'Rejected goods functionality coming soon',
        ]);
    }

    /**
     * Store a newly created rejected good
     */
    public function store(Request $request): JsonResponse
    {
        // Placeholder - will be implemented with DR number dropdown
        return response()->json([
            'success' => true,
            'message' => 'Rejected good stored successfully',
        ]);
    }
}
