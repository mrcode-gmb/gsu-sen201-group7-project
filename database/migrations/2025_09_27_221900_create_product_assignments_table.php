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
        Schema::create('product_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Staff member
            $table->foreignId('purchase_id')->constrained()->onDelete('cascade'); // Product from inventory
            $table->decimal('assigned_quantity', 30, 2); // Quantity assigned to staff
            $table->decimal('sold_quantity', 30, 2)->default(0); // Quantity sold by staff
            $table->decimal('returned_quantity', 30, 2)->default(0); // Quantity returned by staff
            $table->decimal('expected_selling_price', 30, 2); // Expected selling price per unit
            $table->decimal('actual_total_sales', 30, 2)->default(0); // Actual sales amount
            $table->decimal('profit_collected', 30, 2)->default(0); // Profit collected by admin
            $table->enum('status', ['assigned', 'in_progress', 'completed', 'returned'])->default('assigned');
            $table->date('assigned_date');
            $table->date('due_date')->nullable(); // When staff should return
            $table->date('returned_date')->nullable(); // When staff actually returned
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_assignments');
    }
};
