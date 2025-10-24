<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the column is already nullable
        $columns = DB::select("SHOW COLUMNS FROM activity_logs WHERE Field = 'user_id'");
        if (!empty($columns) && $columns[0]->Null === 'YES') {
            // Already nullable, skip
            return;
        }

        // Use raw SQL for MySQL to modify the column
        DB::statement('ALTER TABLE activity_logs MODIFY user_id BIGINT UNSIGNED NULL');
        
        // Drop and recreate the foreign key constraint
        try {
            DB::statement('ALTER TABLE activity_logs DROP FOREIGN KEY activity_logs_user_id_foreign');
        } catch (\Exception $e) {
            // Foreign key might not exist, ignore
        }
        
        DB::statement('
            ALTER TABLE activity_logs 
            ADD CONSTRAINT activity_logs_user_id_foreign 
            FOREIGN KEY (user_id) 
            REFERENCES users(id) 
            ON DELETE CASCADE
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only rollback if there are no NULL values
        $nullCount = DB::table('activity_logs')->whereNull('user_id')->count();
        
        if ($nullCount > 0) {
            throw new \Exception("Cannot rollback: {$nullCount} records have NULL user_id. Please update or delete these records first.");
        }

        // Drop the foreign key
        DB::statement('ALTER TABLE activity_logs DROP FOREIGN KEY activity_logs_user_id_foreign');
        
        // Make column NOT NULL
        DB::statement('ALTER TABLE activity_logs MODIFY user_id BIGINT UNSIGNED NOT NULL');
        
        // Recreate the foreign key
        DB::statement('
            ALTER TABLE activity_logs 
            ADD CONSTRAINT activity_logs_user_id_foreign 
            FOREIGN KEY (user_id) 
            REFERENCES users(id) 
            ON DELETE CASCADE
        ');
    }
};
