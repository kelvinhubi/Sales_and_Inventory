<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Database Setup Route for InfinityFree
|--------------------------------------------------------------------------
|
| This route helps you set up your database on InfinityFree hosting
| where you can't run artisan commands directly from the terminal.
| 
| IMPORTANT: Remove or secure this route after initial setup!
|
*/

Route::get('/setup-database/{token}', function ($token) {
    // Simple security token - replace 'your-secret-token' with a strong, unique token
    if ($token !== 'your-secret-setup-token-2024') {
        abort(404);
    }

    try {
        $output = [];
        
        // Check database connection
        $output[] = "Testing database connection...";
        try {
            DB::connection()->getPdo();
            $output[] = "✅ Database connection successful!";
        } catch (Exception $e) {
            $output[] = "❌ Database connection failed: " . $e->getMessage();
            return response()->json([
                'status' => 'error',
                'output' => $output
            ]);
        }

        // Run migrations
        $output[] = "\nRunning database migrations...";
        Artisan::call('migrate', ['--force' => true]);
        $output[] = "✅ Migrations completed!";
        $output[] = Artisan::output();

        // Run seeders (optional - uncomment if you have seeders)
        /*
        $output[] = "\nRunning database seeders...";
        Artisan::call('db:seed', ['--force' => true]);
        $output[] = "✅ Seeders completed!";
        $output[] = Artisan::output();
        */

        // Clear caches
        $output[] = "\nClearing caches...";
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        $output[] = "✅ Caches cleared and rebuilt!";

        // Create storage link
        $output[] = "\nCreating storage link...";
        if (!file_exists(public_path('storage'))) {
            Artisan::call('storage:link');
            $output[] = "✅ Storage link created!";
        } else {
            $output[] = "ℹ️ Storage link already exists.";
        }

        $output[] = "\n🎉 Database setup completed successfully!";
        $output[] = "\n⚠️  IMPORTANT: For security, remove this route from your routes/web.php file!";

        return response()->json([
            'status' => 'success',
            'output' => $output
        ]);

    } catch (Exception $e) {
        $output[] = "❌ Error occurred: " . $e->getMessage();
        return response()->json([
            'status' => 'error',
            'output' => $output
        ]);
    }
})->name('setup.database');

// Alternative simple HTML view for the setup
Route::get('/database-setup/{token}', function ($token) {
    // Simple security token - replace 'your-secret-token' with a strong, unique token
    if ($token !== 'your-secret-setup-token-2024') {
        abort(404);
    }

    return view('setup.database');
})->name('setup.database.view');