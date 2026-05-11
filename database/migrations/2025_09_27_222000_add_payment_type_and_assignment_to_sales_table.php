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
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'payment_type')) {
                $table->enum('payment_type', ['cash', 'bank_transfer', 'pos', 'mobile_money', 'credit'])->default('cash')->after('customer_phone');
            }
            if (!Schema::hasColumn('sales', 'assignment_id')) {
                $table->foreignId('assignment_id')->nullable()->constrained('product_assignments')->onDelete('set null')->after('customer_phone');
            }
            if (!Schema::hasColumn('sales', 'sale_status')) {
                $table->enum('sale_status', ['pending', 'completed', 'returned'])->default('completed')->after('customer_phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
            $table->dropColumn(['payment_type', 'assignment_id', 'sale_status']);
        });
    }
};
