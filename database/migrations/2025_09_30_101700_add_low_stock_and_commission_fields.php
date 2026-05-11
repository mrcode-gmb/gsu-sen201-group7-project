<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add low stock threshold to products
        Schema::table('products', function (Blueprint $table) {
            $table->integer('low_stock_threshold')->default(10)->after('current_stock');
        });

        // Add commission fields to product_assignments
        Schema::table('product_assignments', function (Blueprint $table) {
            $table->decimal('commission_rate', 30, 2)->default(0)->after('expected_selling_price');
            $table->decimal('commission_amount', 30, 2)->default(0)->after('commission_rate');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('low_stock_threshold');
        });

        Schema::table('product_assignments', function (Blueprint $table) {
            $table->dropColumn(['commission_rate', 'commission_amount']);
        });
    }
};
