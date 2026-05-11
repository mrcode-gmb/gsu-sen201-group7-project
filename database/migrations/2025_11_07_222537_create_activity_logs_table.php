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
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('business_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('action'); // created, updated, deleted, logged_in, etc.
            $table->string('model')->nullable(); // Business, Product, Sale, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('description');
            $table->json('properties')->nullable(); // Additional data
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->index(['business_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('action');
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
