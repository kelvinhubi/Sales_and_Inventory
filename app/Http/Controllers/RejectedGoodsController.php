<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Branch;
use App\Models\Product;
use App\Models\RejectedGood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RejectedGoodsController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('Login');
        }
        $rejectedGoods = RejectedGood::with(['brand', 'branch', 'items.product'])->paginate(10);
        return view('owner.rejected-goods.index', compact('rejectedGoods'));
    }

    public function create()
    {
        if (!Auth::check()) {
            return redirect()->route('Login');
        }
        $brands = Brand::all();
        $branches = Branch::all();
        $products = Product::select('id', 'name', 'price')->get();
        
        // Get available DR numbers from past orders
        $drNumbers = \App\Models\PastOrder::whereNotNull('dr_number')
                     ->orderBy('created_at', 'desc')
                     ->pluck('dr_number', 'dr_number')
                     ->toArray();
                     
        return view('owner.rejected-goods.create', compact('brands', 'branches', 'products', 'drNumbers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'brand_id' => 'required|exists:brands,id',
            'branch_id' => 'required|exists:branches,id',
            'dr_no' => 'required|unique:rejected_goods',
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string',
            'product_items.*.product_id' => 'required|exists:products,id',
            'product_items.*.quantity' => 'required|integer|min:1',
        ]);

        $rejectedGood = RejectedGood::create($validated);

        foreach ($validated['product_items'] as $item) {
            $rejectedGood->items()->create($item);
        }

        return redirect()->route('owner.rejected-goods.index')->with('success', 'Rejected good created successfully.');
    }

    public function show(RejectedGood $rejectedGood)
    {
        if (!Auth::check()) {
            return redirect()->route('Login');
        }
        $this->authorize('view', $rejectedGood);
        $rejectedGood->load(['brand', 'branch', 'items.product']);
        return view('owner.rejected-goods.show', compact('rejectedGood'));
    }

    public function edit(RejectedGood $rejectedGood)
    {
        if (!Auth::check()) {
            return redirect()->route('Login');
        }
        $this->authorize('update', $rejectedGood);
        $brands = Brand::all();
        $branches = Branch::all();
        return view('owner.rejected-goods.edit', compact('rejectedGood', 'brands', 'branches'));
    }

    public function update(Request $request, RejectedGood $rejectedGood)
    {
        $this->authorize('update', $rejectedGood);

        $validated = $request->validate([
            'date' => 'required|date',
            'brand_id' => 'required|exists:brands,id',
            'branch_id' => 'required|exists:branches,id',
            'dr_no' => 'required|unique:rejected_goods,dr_no,' . $rejectedGood->id,
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string',
            'product_items.*.product_id' => 'required|exists:products,id',
            'product_items.*.quantity' => 'required|integer|min:1',
        ]);

        $rejectedGood->update($validated);

        $rejectedGood->items()->delete();
        foreach ($validated['product_items'] as $item) {
            $rejectedGood->items()->create($item);
        }

        return redirect()->route('owner.rejected-goods.index')->with('success', 'Rejected good updated successfully.');
    }

    public function destroy(RejectedGood $rejectedGood)
    {
        $this->authorize('delete', $rejectedGood);
        $rejectedGood->delete();
        return redirect()->route('owner.rejected-goods.index')->with('success', 'Rejected good deleted successfully.');
    }

    public function getDrDetails($drNumber)
    {
        $pastOrder = \App\Models\PastOrder::where('dr_number', $drNumber)
                     ->with(['brand', 'branch'])
                     ->first();
        
        if (!$pastOrder) {
            return response()->json(['error' => 'DR number not found'], 404);
        }
        
        return response()->json([
            'brand_id' => $pastOrder->brand_id,
            'brand_name' => $pastOrder->brand->name,
            'branch_id' => $pastOrder->branch_id,
            'branch_name' => $pastOrder->branch->name,
        ]);
    }
}