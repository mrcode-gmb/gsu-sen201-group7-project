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
        Schema::create('sale_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_assignment_id')->constrained('product_assignments')->onDelete('cascade');
            $table->string('customer_type')->nullable();
            $table->decimal('sale_price', 50, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_prices');
    }
};
