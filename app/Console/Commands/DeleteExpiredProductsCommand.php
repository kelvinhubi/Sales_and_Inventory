<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class DeleteExpiredProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:delete-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired products from the database';

    /**
     * Execute the console command.
     */
    
    public function handle()
    {
        try {
            // Log that the scheduled task is starting
            Log::info('Starting scheduled task to delete expired products');
            
            // Get all expired products
            $expiredProducts = Product::expired()->get();
            $deletedCount = 0;
            
            // Delete each expired product
            foreach ($expiredProducts as $product) {
                $product->delete();
                $deletedCount++;
            }
            
            // Log the results
            Log::info("Expired products deletion completed. Deleted {$deletedCount} products.");
            
            // Output to console
            $this->info("Successfully deleted {$deletedCount} expired products.");
            
        } catch (\Exception $e) {
            // Log any errors
            Log::error('Error deleting expired products: ' . $e->getMessage());
            
            // Output error to console
            $this->error('Failed to delete expired products: ' . $e->getMessage());
            
            // Return error code
            return 1;
        }
        
        return 0;
    }
}