<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->date('date');
            $table->string('name'); // e.g. "Daily Expenses Summary"
            $table->decimal('today_profit', 10, 2); // today_profit for the day
            $table->decimal('amount', 10, 2); // total for the day
            $table->decimal('today_net_profit', 10, 2); // total for the day
            $table->text('notes')->nullable(); // breakdown list
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
