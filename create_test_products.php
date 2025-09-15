<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Set up Laravel database connection
$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'sales_and_inventory',
    'username'  => 'root',
    'password'  => 'Kelvinfo14',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Create test products
echo "Creating test products...\n";

// Create an expired product (expired yesterday)
Capsule::table('products')->insert([
    'name' => 'Expired Milk',
    'price' => 4.99,
    'quantity' => 10,
    'perishable' => 'yes',
    'expiration_date' => '2025-08-18',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
]);
echo "Created expired product: Expired Milk\n";

// Create a product that expires today
Capsule::table('products')->insert([
    'name' => 'Milk Today',
    'price' => 3.99,
    'quantity' => 5,
    'perishable' => 'yes',
    'expiration_date' => '2025-08-19',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
]);
echo "Created product expiring today: Milk Today\n";

// Create a product that expires tomorrow
Capsule::table('products')->insert([
    'name' => 'Bread Tomorrow',
    'price' => 2.49,
    'quantity' => 15,
    'perishable' => 'yes',
    'expiration_date' => '2025-08-20',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
]);
echo "Created product expiring tomorrow: Bread Tomorrow\n";

// Create a product that expires in 7 days
Capsule::table('products')->insert([
    'name' => 'Fruits in Week',
    'price' => 5.99,
    'quantity' => 20,
    'perishable' => 'yes',
    'expiration_date' => '2025-08-26',
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
]);
echo "Created product expiring in 7 days: Fruits in Week\n";

// Create a non-perishable product (no expiration date)
Capsule::table('products')->insert([
    'name' => 'Laptop',
    'price' => 999.99,
    'quantity' => 3,
    'perishable' => 'no',
    'expiration_date' => null,
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
]);
echo "Created non-perishable product: Laptop\n";

echo "All test products created successfully!\n";

// List all products
echo "\nCurrent products in database:\n";
$products = Capsule::table('products')->get();
foreach ($products as $product) {
    echo "- {$product->name} (ID: {$product->id}) - Exp: " . ($product->expiration_date ?? 'N/A') . "\n";
}