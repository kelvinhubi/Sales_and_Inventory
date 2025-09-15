<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

echo "Checking past orders data..." . PHP_EOL;

try {
    $pastOrdersCount = App\Models\PastOrder::count();
    echo "Past Orders count: " . $pastOrdersCount . PHP_EOL;

    $brandsCount = App\Models\Brand::count();
    echo "Brands count: " . $brandsCount . PHP_EOL;

    $productsCount = App\Models\Product::count();
    echo "Products count: " . $productsCount . PHP_EOL;

    // If no past orders exist, create some test data
    if ($pastOrdersCount === 0) {
        echo "Creating test past orders..." . PHP_EOL;

        // Create brands if they don't exist
        if ($brandsCount === 0) {
            App\Models\Brand::create(['name' => 'Test Brand A']);
            App\Models\Brand::create(['name' => 'Test Brand B']);
            echo "Created test brands." . PHP_EOL;
        }

        // Create branches if they don't exist
        $branchesCount = App\Models\Branch::count();
        if ($branchesCount === 0) {
            App\Models\Branch::create(['name' => 'Main Branch']);
            App\Models\Branch::create(['name' => 'Secondary Branch']);
            echo "Created test branches." . PHP_EOL;
        }

        $brand1 = App\Models\Brand::first();
        $branch1 = App\Models\Branch::first();
        $product1 = App\Models\Product::first();

        if ($brand1 && $branch1 && $product1) {
            // Create test past order
            $pastOrder = App\Models\PastOrder::create([
                'brand_id' => $brand1->id,
                'branch_id' => $branch1->id,
                'total_amount' => 100.00
            ]);

            // Create test past order items
            App\Models\PastOrderItem::create([
                'past_order_id' => $pastOrder->id,
                'product_id' => $product1->id,
                'quantity' => 5,
                'price' => 20.00
            ]);

            echo "Created test past order with items." . PHP_EOL;
        }
    }

    echo "Data check completed." . PHP_EOL;

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}