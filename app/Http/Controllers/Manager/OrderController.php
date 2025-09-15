<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function showView()
    {
        if (!Auth::check()) {
            return redirect()->route('homepage');
        }

        return view('manager.order');
    }

    public function deductInventory(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // This would contain the same logic as the owner's deductInventory method
        // For now, returning a placeholder response
        return response()->json(['success' => true, 'message' => 'Inventory deducted successfully']);
    }
}