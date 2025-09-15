<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    //
    public function showView():View|RedirectResponse{
        if (!Auth::check()) {
            return redirect()->route('Login');
        }
        return view('owner.products');
    }
    /**
     * Handle the request to add a new product.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkstock($id){
        $product = Product::find($id);
        if($product && $product->quantity <= 0){
            $product->quantity = 0;
            $product->save();
            return response()->json(['message' => 'Product quantity set to zero']);
        }
        return response()->json(['message' => 'Product quantity is not zero']);
    }
    
    /**
     * Display a listing of products
     */
    public function index(Request $request): JsonResponse
    {
        // Automatically delete expired products when accessing the product list
        $this->deleteExpiredProducts();
        
        $query = Product::query();

        // Get all products for dropdowns if 'all' parameter is present
        if ($request->has('all')) {
            $products = $query->get(['id', 'name']);
            return response()->json($products);
        }
        // Search by product name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by perishable status
        if ($request->filled('perishable')) {
            $query->perishable($request->perishable);
        }

        // Filter by stock level
        if ($request->filled('stock')) {
            switch ($request->stock) {
                case 'low':
                    $query->lowStock();//method name is scopeLowStock() in the Product model
                    break;
                case 'out':
                    $query->outOfStock();//method name is scopeOutOfStock() in the Product model
                    break;
            }
        }

        // Order by name
        $query->orderBy('name', 'asc');

        // Pagination
        $perPage = $request->get('per_page', 10);
        $products = $query->paginate($perPage);

        return response()->json($products);
    }

      public function store(StoreProductRequest $request): JsonResponse
   {
       $validatedData = $request->validated();
       
       // Ensure expiration_date is properly handled
       if (isset($validatedData['expiration_date'])) {
           $validatedData['expiration_date'] = $validatedData['expiration_date'] ?: null;
       }

       $product = Product::create($validatedData);

       return response()->json([
           'message' => 'Product created successfully',
           'data' => $product
       ], 201);
   }

    /**
     * Display the specified product
     */
    public function show(Product $product): JsonResponse
    {
        // Check if the product has expired and delete it if so
        if ($product->hasExpired()) {
            $product->delete();
            return response()->json([
                'error' => 'Product not found or has expired',
                'message' => 'The requested product has expired and has been deleted'
            ], 404);
        }
        
        return response()->json([
            'data' => $product
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $validatedData = $request->validated();
        
        // Ensure expiration_date is properly handled
        if (isset($validatedData['expiration_date'])) {
            $validatedData['expiration_date'] = $validatedData['expiration_date'] ?: null;
        }

        $product->update($validatedData);

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product->fresh()
        ]);
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

    /**
     * Get product statistics
     */
    /**
     * Get product statistics
     */
    public function stats(): JsonResponse
    {
        // Automatically delete expired products when getting stats
        $this->deleteExpiredProducts();
        
        $stats = [
            'total_products' => Product::count(),
            'total_quantity' => Product::sum('quantity'),
            'perishable_products' => Product::perishable('yes')->count(),
            'low_stock_products' => Product::lowStock()->count(),
            'out_of_stock_products' => Product::outOfStock()->count(),
            'average_price' => Product::avg('price')
        ];

        return response()->json($stats);
    }
    
    /**
     * Delete all expired products
     */
    public function deleteExpiredProducts(): JsonResponse
    {
        try {
            $expiredProducts = Product::expired()->get();
            $deletedCount = 0;
            
            foreach ($expiredProducts as $product) {
                $product->delete();
                $deletedCount++;
            }
            
            return response()->json([
                'message' => "Successfully deleted {$deletedCount} expired products",
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting expired products: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to delete expired products',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
}
