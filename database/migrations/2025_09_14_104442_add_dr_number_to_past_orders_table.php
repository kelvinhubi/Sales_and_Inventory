<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('past_orders', function (Blueprint $table) {
            $table->string('dr_number')->nullable()->after('id');
            $table->index('dr_number'); // Add index for faster lookups
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('past_orders', function (Blueprint $table) {
            $table->dropIndex(['dr_number']);
            $table->dropColumn('dr_number');
        });
    }
};
