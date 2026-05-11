<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('name')->constrained('categories')->nullOnDelete();
            $table->string('sku')->nullable()->after('category_id');
            $table->string('storage_location')->nullable()->after('sku');
            $table->unique(['business_id', 'sku']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['business_id', 'sku']);
            $table->dropConstrainedForeignId('category_id');
            $table->dropColumn(['sku', 'storage_location']);
        });
    }
};
