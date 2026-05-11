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
            //
            $table->string('unique_id')->unique()->after('id')->nullable();
        });

        // Generate unique IDs for existing records
        \App\Models\Sale::chunk(100, function ($sales) {
            foreach ($sales as $sale) {
                $sale->update([
                    'unique_id' => 'SALE-' . strtoupper(Str::random(8)) . '-' . $sale->id
                ]);
            }
        });

        // Make the column not nullable after populating
        Schema::table('sales', function (Blueprint $table) {
            $table->string('unique_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
             $table->dropColumn('unique_id');
        });
    }
};
