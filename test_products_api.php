<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\DB;

// Test the products API logic
$query = Product::query()
    ->select('products.*', DB::raw('COALESCE(products.original_price, 0) as original_cost'));

$products = $query->paginate(2);

// Map in computed profit per item (unit) for UI convenience
$products->getCollection()->transform(function($p){
    $p->original_cost = (float)($p->original_cost ?? 0);
    $p->profit = round(((float)$p->price - $p->original_cost), 2);
    return $p;
});

echo json_encode($products->items(), JSON_PRETTY_PRINT);