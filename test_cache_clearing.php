<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Services\AIBusinessIntelligenceService;
use Illuminate\Support\Facades\Cache;

echo "=== TESTING AI INSIGHTS CACHE CLEARING ===\n";

// Get initial insights to populate cache
$service = new AIBusinessIntelligenceService();
$initialInsights = $service->getBusinessInsights();
echo "✅ Initial insights loaded (cache populated)\n";
echo "Initial out of stock count: " . count(array_filter($initialInsights['recommendations'], function($rec) {
    return stripos($rec['title'], 'out of stock') !== false;
})) . "\n\n";

// Update a product to trigger cache clearing
$product = Product::where('quantity', 0)->first();
if ($product) {
    echo "📝 Updating product: {$product->name}\n";
    echo "   Before: quantity = {$product->quantity}\n";
    
    // Update the product (this should clear the cache)
    $product->quantity = 50;
    $product->save();
    
    echo "   After: quantity = {$product->quantity}\n";
    echo "✅ Product updated (cache should be cleared)\n\n";
    
    // Get fresh insights (should be recalculated, not from cache)
    $newInsights = $service->getBusinessInsights();
    echo "✅ New insights loaded\n";
    
    $newOutOfStockCount = count(array_filter($newInsights['recommendations'], function($rec) {
        return stripos($rec['title'], 'out of stock') !== false;
    }));
    
    echo "New out of stock count: " . $newOutOfStockCount . "\n";
    
    if ($newOutOfStockCount < count(array_filter($initialInsights['recommendations'], function($rec) {
        return stripos($rec['title'], 'out of stock') !== false;
    }))) {
        echo "🎉 SUCCESS: AI insights updated after product change!\n";
    } else {
        echo "⚠️ Cache may not have been cleared properly\n";
    }
    
    // Reset the product back to test condition
    $product->quantity = 0;
    $product->save();
    echo "🔄 Product reset for future testing\n";
    
} else {
    echo "❌ No out of stock products found to test with\n";
}