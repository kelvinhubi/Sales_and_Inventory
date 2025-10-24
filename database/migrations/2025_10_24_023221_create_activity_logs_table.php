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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('user_name');
            $table->string('user_role');
            $table->string('action_type'); // login, logout, create, update, delete, password_change, etc.
            $table->string('module'); // products, orders, brands, branches, users, etc.
            $table->string('description');
            $table->text('details')->nullable(); // JSON data with more info
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('user_id');
            $table->index('action_type');
            $table->index('module');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
