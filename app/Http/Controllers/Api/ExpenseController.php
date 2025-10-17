<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Expense::query();
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('from')) {
            $query->where('date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('date', '<=', $request->to);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $expenses = $query->orderBy('date', 'desc')->get();
        $total = $expenses->sum('amount');

        return response()->json(['success' => true, 'data' => $expenses, 'total' => $total]);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'date' => 'required|date',
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'brand_id' => 'nullable|exists:brands,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $expense = Expense::create($validatedData);

        return response()->json(['success' => true, 'data' => $expense], 201);
    }

    public function show(Expense $expense): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $expense]);
    }

    public function update(Request $request, Expense $expense): JsonResponse
    {
        $validatedData = $request->validate([
            'date' => 'sometimes|required|date',
            'category' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'notes' => 'nullable|string',
            'brand_id' => 'nullable|exists:brands,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $expense->update($validatedData);

        return response()->json(['success' => true, 'data' => $expense]);
    }

    public function destroy(Expense $expense): JsonResponse
    {
        $expense->delete();

        return response()->json(['success' => true]);
    }
}
