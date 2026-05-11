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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('unit_type', ['Customize', 'Uncustomize']);
            $table->decimal('current_stock', 65, 4)->default(0);
            // $table->decimal('purchase_price')->nullable();
            // $table->decimal('selling_price', 65, 4);
            // $table->decimal('seller_profit', 65, 4);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
