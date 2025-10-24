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
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite: Recreate the table with nullable user_id
            $this->recreateTableForSQLite();
        } else {
            // MySQL/PostgreSQL: Modify column directly
            $this->modifyColumnForMySQL();
        }
    }

    /**
     * SQLite doesn't support ALTER COLUMN, so we need to recreate the table
     */
    protected function recreateTableForSQLite(): void
    {
        // Get existing data
        $logs = DB::table('activity_logs')->get();

        // Drop the existing table
        Schema::dropIfExists('activity_logs');

        // Recreate with nullable user_id
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('user_name');
            $table->string('user_role');
            $table->string('action_type');
            $table->string('module');
            $table->string('description');
            $table->text('details')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('action_type');
            $table->index('module');
            $table->index('created_at');
        });

        // Restore data
        foreach ($logs as $log) {
            DB::table('activity_logs')->insert((array) $log);
        }
    }

    /**
     * MySQL/PostgreSQL can modify columns directly
     */
    protected function modifyColumnForMySQL(): void
    {
        // For MySQL
        if (DB::getDriverName() === 'mysql') {
            // Check if already nullable
            $columns = DB::select("SHOW COLUMNS FROM activity_logs WHERE Field = 'user_id'");
            if (!empty($columns) && $columns[0]->Null === 'YES') {
                return; // Already nullable
            }

            DB::statement('ALTER TABLE activity_logs MODIFY user_id BIGINT UNSIGNED NULL');
            
            try {
                DB::statement('ALTER TABLE activity_logs DROP FOREIGN KEY activity_logs_user_id_foreign');
                DB::statement('
                    ALTER TABLE activity_logs 
                    ADD CONSTRAINT activity_logs_user_id_foreign 
                    FOREIGN KEY (user_id) 
                    REFERENCES users(id) 
                    ON DELETE CASCADE
                ');
            } catch (\Exception $e) {
                // Foreign key operations might fail, continue
            }
        } else {
            // For PostgreSQL
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->foreignId('user_id')->nullable()->change();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if there are NULL values
        $nullCount = DB::table('activity_logs')->whereNull('user_id')->count();
        
        if ($nullCount > 0) {
            throw new \Exception("Cannot rollback: {$nullCount} records have NULL user_id. Please update or delete these records first.");
        }

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite: Recreate table with non-nullable user_id
            $logs = DB::table('activity_logs')->get();

            Schema::dropIfExists('activity_logs');

            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('user_name');
                $table->string('user_role');
                $table->string('action_type');
                $table->string('module');
                $table->string('description');
                $table->text('details')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('low');
                $table->timestamps();
                
                $table->index('user_id');
                $table->index('action_type');
                $table->index('module');
                $table->index('created_at');
            });

            foreach ($logs as $log) {
                DB::table('activity_logs')->insert((array) $log);
            }
        } else {
            // MySQL
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE activity_logs DROP FOREIGN KEY activity_logs_user_id_foreign');
                DB::statement('ALTER TABLE activity_logs MODIFY user_id BIGINT UNSIGNED NOT NULL');
                DB::statement('
                    ALTER TABLE activity_logs 
                    ADD CONSTRAINT activity_logs_user_id_foreign 
                    FOREIGN KEY (user_id) 
                    REFERENCES users(id) 
                    ON DELETE CASCADE
                ');
            } else {
                Schema::table('activity_logs', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                    $table->foreignId('user_id')->nullable(false)->change();
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                });
            }
        }
    }
};
