<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new columns first (avoid MySQL-specific placement modifiers for portability)
        Schema::table('branches', function (Blueprint $table) {
            $table->string('contact_person')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
        });

        // Then rename the contact column using Schema builder (requires doctrine/dbal)
        Schema::table('branches', function (Blueprint $table) {
            $table->renameColumn('contact', 'contact_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First rename back the column
        Schema::table('branches', function (Blueprint $table) {
            $table->renameColumn('contact_number', 'contact');
        });

        // Then drop the added columns
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['contact_person', 'status']);
        });
    }
};
