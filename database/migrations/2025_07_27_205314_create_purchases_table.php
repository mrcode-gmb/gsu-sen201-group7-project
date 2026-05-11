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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Admin who recorded the purchase
            $table->string('supplier_name')->nullable();
            $table->string('supplier_phone')->nullable();
            $table->decimal('quantity', 50, 2)->nullable();
            $table->decimal('purchase_price', 50, 2)->nullable();
            $table->decimal('total_cost', 50, 2)->nullable();
            $table->decimal('selling_price', 50, 2)->nullable();
            $table->decimal('seller_profit', 50, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->text('notes')->nullable()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
