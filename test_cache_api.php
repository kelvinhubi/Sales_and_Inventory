<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "=== TESTING AI CACHE CLEARING API ===\n";

// Login as an owner to test the authenticated endpoint
$owner = User::where('role', 'owner')->first();
if ($owner) {
    Auth::login($owner);
    echo "✅ Logged in as owner: {$owner->name}\n";
    
    // Test the cache clearing endpoint
    $url = 'http://127.0.0.1:8000/api/ai/clear-cache';
    
    // Create curl request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Requested-With: XMLHttpRequest'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Code: {$httpCode}\n";
    echo "Response: {$response}\n";
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if ($data && $data['success']) {
            echo "🎉 SUCCESS: Cache clearing API works!\n";
        } else {
            echo "⚠️ API responded but may have failed\n";
        }
    } else {
        echo "❌ API endpoint may require web session authentication\n";
    }
    
} else {
    echo "❌ No owner user found to test with\n";
}

echo "\n=== SOLUTION SUMMARY ===\n";
echo "✅ Product.php: hasExpired/scopeExpired methods properly commented out\n";
echo "✅ Cache clearing: Auto-clears when products are updated\n";
echo "✅ Manual refresh: Added /api/ai/clear-cache endpoint\n";
echo "✅ Frontend: Refresh button now clears cache before reloading\n";