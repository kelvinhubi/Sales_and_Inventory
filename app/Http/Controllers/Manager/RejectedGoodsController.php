<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Product;
use App\Models\RejectedGood;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RejectedGoodsController extends Controller
{
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
            return redirect()->route('homepage');
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
        if (! Auth::check()) {
            return redirect()->route('homepage');
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'brand_id' => 'required|exists:brands,id',
            'branch_id' => 'required|exists:branches,id',
            'dr_no' => 'required|unique:rejected_goods',
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string',
            'product_items.*.product_id' => 'required|exists:products,id',
            'product_items.*.quantity' => 'required|integer|min:1',
            'product_items.*.price' => 'required|numeric|min:0',
            'product_items.*.reason' => 'nullable|string',
        ]);

        try {
            // Create rejected goods record
            $rejectedGood = RejectedGood::create([
                'date' => $validated['date'],
                'brand_id' => $validated['brand_id'],
                'branch_id' => $validated['branch_id'],
                'dr_no' => $validated['dr_no'],
                'amount' => $validated['amount'],
                'reason' => $validated['reason'],
            ]);

            // Create rejected goods items
            if (isset($validated['product_items'])) {
                foreach ($validated['product_items'] as $item) {
                    $rejectedGood->items()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'reason' => $item['reason'] ?? null,
                    ]);
                }
            }

            return redirect()->route('manager.rejected-goods.index')
                           ->with('success', 'Rejected goods record created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to create rejected goods record: ' . $e->getMessage()]);
        }
    }

    public function show(RejectedGood $rejectedGood)
    {
        if (! Auth::check()) {
            return redirect()->route('homepage');
        }

        $rejectedGood->load(['brand', 'branch', 'items.product']);

        return view('manager.rejected-goods.show', compact('rejectedGood'));
    }

    public function destroy(RejectedGood $rejectedGood)
    {
        if (! Auth::check()) {
            return redirect()->route('homepage');
        }

        try {
            // Delete related items first
            $rejectedGood->items()->delete();

            // Delete the rejected goods record
            $rejectedGood->delete();

            return redirect()->route('manager.rejected-goods.index')
                           ->with('success', 'Rejected goods record deleted successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete rejected goods record: ' . $e->getMessage()]);
        }
    }

    public function getDrDetails($drNumber)
    {
        if (! Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $pastOrder = \App\Models\PastOrder::with(['items.product', 'brand', 'branch'])
                                              ->where('dr_number', $drNumber)
                                              ->first();

            if (! $pastOrder) {
                return response()->json(['error' => 'DR number not found'], 404);
            }

            $response = [
                'brand_id' => $pastOrder->brand_id,
                'branch_id' => $pastOrder->branch_id,
                'total_amount' => $pastOrder->total_amount,
                'items' => $pastOrder->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? 'Unknown',
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'total' => $item->quantity * $item->price,
                    ];
                }),
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch DR details: ' . $e->getMessage()], 500);
        }
    }
}
