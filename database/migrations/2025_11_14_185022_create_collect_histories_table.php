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
        Schema::create('collect_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('collected_by')->constrained('users')->onDelete('cascade');
            $table->decimal('collected_quantity', 10, 2);
            $table->decimal('remaining_quantity_before', 10, 2);
            $table->decimal('remaining_quantity_after', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamp('collected_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collect_histories');
    }
};
