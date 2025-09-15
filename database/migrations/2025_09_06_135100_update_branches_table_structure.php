<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\StringType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new columns first
        Schema::table('branches', function (Blueprint $table) {
            $table->string('contact_person')->after('address')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active')->after('contact');
        });

        // Then rename the contact column
        DB::statement('ALTER TABLE branches CHANGE contact contact_number varchar(255)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First rename back the column
        DB::statement('ALTER TABLE branches CHANGE contact_number contact varchar(255)');

        // Then drop the added columns
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['contact_person', 'status']);
        });
    }
};
