<?php

/**
 * This script assigns existing orders (without user_id) to users.
 * Run this with: php assign_orders_to_users.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Order;
use App\Models\User;

echo "Starting order assignment...\n\n";

// Get all orders without user_id
$ordersWithoutUser = Order::whereNull('user_id')->get();

if ($ordersWithoutUser->isEmpty()) {
    echo "✓ All orders already have a user assigned.\n";
    exit(0);
}

echo "Found {$ordersWithoutUser->count()} orders without user_id.\n";

// Get the first owner/admin user
$firstUser = User::where('Role', 'Owner')
    ->orWhere('Role', 'owner')
    ->first();

if (!$firstUser) {
    // If no owner, get the first user
    $firstUser = User::first();
}

if (!$firstUser) {
    echo "✗ Error: No users found in the database.\n";
    echo "  Please create at least one user first.\n";
    exit(1);
}

echo "Assigning orders to user: {$firstUser->name} (ID: {$firstUser->id})\n\n";

// Assign each order to the first user
foreach ($ordersWithoutUser as $order) {
    $order->user_id = $firstUser->id;
    $order->save();
    echo "✓ Order #{$order->id} assigned to user #{$firstUser->id}\n";
}

echo "\n✓ Successfully assigned {$ordersWithoutUser->count()} orders.\n";
echo "  All orders now have a user assigned.\n";
