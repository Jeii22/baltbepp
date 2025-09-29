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
        // For SQLite compatibility, drop and recreate the column
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('payment_method', 50)->default('cod');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('payment_method', ['cod','gcash','paymaya','card'])->default('cod');
        });
    }
};