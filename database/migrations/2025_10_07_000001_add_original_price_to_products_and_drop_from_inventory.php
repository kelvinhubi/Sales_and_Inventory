<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        // Add original_price to products if not exists
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'original_price')) {
                // Avoid MySQL-specific column placement for SQLite portability
                $table->decimal('original_price', 10, 2)->nullable();
            }
        });

        // Migrate existing data from inventory.original_price to products.original_price
        if (Schema::hasTable('inventory') && Schema::hasColumn('inventory', 'original_price')) {
            // Use a portable approach instead of MySQL-specific UPDATE ... JOIN
            DB::table('inventory')
                ->whereNotNull('original_price')
                ->select('product_id', 'original_price')
                ->orderBy('product_id')
                ->chunk(1000, function ($rows) {
                    foreach ($rows as $row) {
                        DB::table('products')
                            ->where('id', $row->product_id)
                            ->update(['original_price' => $row->original_price]);
                    }
                });
        }

        // Drop original_price from inventory if exists
        if (Schema::hasTable('inventory') && Schema::hasColumn('inventory', 'original_price')) {
            Schema::table('inventory', function (Blueprint $table) {
                $table->dropColumn('original_price');
            });
        }
    }

    public function down(): void
    {
        // Re-add column to inventory
        if (Schema::hasTable('inventory') && ! Schema::hasColumn('inventory', 'original_price')) {
            Schema::table('inventory', function (Blueprint $table) {
                // Avoid MySQL-specific column placement for SQLite portability
                $table->decimal('original_price', 10, 2)->nullable();
            });
        }

        // Move data back from products to inventory where possible
        if (Schema::hasTable('inventory') && Schema::hasColumn('inventory', 'original_price')) {
            DB::table('products')
                ->whereNotNull('original_price')
                ->select('id', 'original_price')
                ->orderBy('id')
                ->chunk(1000, function ($rows) {
                    foreach ($rows as $row) {
                        DB::table('inventory')
                            ->where('product_id', $row->id)
                            ->update(['original_price' => $row->original_price]);
                    }
                });
        }

        // Drop from products
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'original_price')) {
                $table->dropColumn('original_price');
            }
        });
    }
};
