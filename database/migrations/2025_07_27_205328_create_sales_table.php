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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Salesperson who made the sale
            $table->decimal('quantity', 10, 2);
            $table->decimal('selling_price_per_unit', 10, 2);
            $table->decimal('cost_price_per_unit', 10, 2); // For profit calculation
            $table->decimal('total_amount', 10, 2);
            $table->decimal('total_cost', 10, 2);
            $table->decimal('profit', 10, 2);
            $table->decimal('seller_profit_per_unit', 10, 2);
            $table->decimal('net_profit_per_unit', 10, 2);
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->date('sale_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
