<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Product;
use App\Models\RejectedGood;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RejectedGoodsController extends Controller
{
    use LogsActivity;
    public function index()
    {
        if (! Auth::check()) {
            return redirect()->route('homepage');
        }

        $rejectedGoods = RejectedGood::with(['brand', 'branch', 'items.product'])->paginate(10);

        return view('manager.rejected-goods.index', compact('rejectedGoods'));
    }

    public function create()
    {
        if (! Auth::check()) {
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

        return view('manager.rejected-goods.create', compact('brands', 'branches', 'products', 'drNumbers'));
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
            'product_items' => 'required|array|min:1',
            'product_items.*.product_id' => 'required|exists:products,id',
            'product_items.*.quantity' => 'required|integer|min:1',
        ], [
            'brand_id.required' => 'Please select a DR Number to populate brand information.',
            'branch_id.required' => 'Please select a DR Number to populate branch information.',
            'dr_no.required' => 'DR Number is required.',
            'dr_no.unique' => 'This DR Number has already been used for a rejected goods record.',
            'product_items.required' => 'Please add at least one product item.',
            'product_items.min' => 'Please add at least one product item.',
            'product_items.*.product_id.required' => 'Please select a product for each item.',
            'product_items.*.quantity.required' => 'Please enter a quantity for each item.',
            'product_items.*.quantity.min' => 'Quantity must be at least 1.',
        ]);

        $rejectedGood = RejectedGood::create($validated);

        foreach ($validated['product_items'] as $item) {
            $rejectedGood->items()->create($item);
        }

        // Log rejected goods creation
        self::logActivity(
            'create',
            'rejected_goods',
            "Created rejected goods record - DR No: {$rejectedGood->dr_no}",
            [
                'rejected_good_id' => $rejectedGood->id,
                'dr_no' => $rejectedGood->dr_no,
                'amount' => $rejectedGood->amount,
                'items_count' => count($validated['product_items'])
            ],
            'medium'
        );

        return redirect()->route('manager.rejected-goods.index')->with('success', 'Rejected good created successfully.');
    }

    public function show(RejectedGood $rejectedGood)
    {
        if (! Auth::check()) {
            return redirect()->route('Login');
        }
        $this->authorize('view', $rejectedGood);
        $rejectedGood->load(['brand', 'branch', 'items.product']);

        return view('manager.rejected-goods.show', compact('rejectedGood'));
    }

    public function edit(RejectedGood $rejectedGood)
    {
        if (! Auth::check()) {
            return redirect()->route('Login');
        }
        $this->authorize('update', $rejectedGood);
        $brands = Brand::all();
        $branches = Branch::all();

        return view('manager.rejected-goods.edit', compact('rejectedGood', 'brands', 'branches'));
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

        // Log rejected goods update
        self::logActivity(
            'update',
            'rejected_goods',
            "Updated rejected goods record - DR No: {$rejectedGood->dr_no}",
            [
                'rejected_good_id' => $rejectedGood->id,
                'dr_no' => $rejectedGood->dr_no,
                'amount' => $rejectedGood->amount
            ],
            'medium'
        );

        return redirect()->route('manager.rejected-goods.index')->with('success', 'Rejected good updated successfully.');
    }

    public function destroy(RejectedGood $rejectedGood)
    {
        $this->authorize('delete', $rejectedGood);
        
        $drNo = $rejectedGood->dr_no;
        $rejectedGoodId = $rejectedGood->id;
        
        $rejectedGood->delete();

        // Log rejected goods deletion
        self::logActivity(
            'delete',
            'rejected_goods',
            "Deleted rejected goods record - DR No: {$drNo}",
            [
                'rejected_good_id' => $rejectedGoodId,
                'dr_no' => $drNo
            ],
            'high'
        );

        return redirect()->route('manager.rejected-goods.index')->with('success', 'Rejected good deleted successfully.');
    }

    public function getDrDetails($drNumber)
    {
        $pastOrder = \App\Models\PastOrder::where('dr_number', $drNumber)
                     ->with(['brand', 'branch'])
                     ->first();

        if (! $pastOrder) {
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
