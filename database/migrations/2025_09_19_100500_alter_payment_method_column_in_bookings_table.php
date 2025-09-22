<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert ENUM('cod','gcash','paymaya','card') to a flexible VARCHAR(50)
        DB::statement("ALTER TABLE bookings MODIFY COLUMN payment_method VARCHAR(50) NOT NULL DEFAULT 'cod'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the original ENUM definition
        DB::statement("ALTER TABLE bookings MODIFY COLUMN payment_method ENUM('cod','gcash','paymaya','card') NOT NULL DEFAULT 'cod'");
    }
};