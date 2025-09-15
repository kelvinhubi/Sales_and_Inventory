<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    public function getSalesData(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => []]);
    }

    public function getProductSalesData(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => []]);
    }

    public function getTopBottomBrands(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => []]);
    }

    public function getTopBottomBranches(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => []]);
    }

    public function getTopBottomProducts(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => []]);
    }
}
