<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('total_amount');
            }
            if (!Schema::hasColumn('bookings', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_method');
                $table->index('payment_reference');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'payment_reference')) {
                $table->dropIndex(['payment_reference']);
                $table->dropColumn('payment_reference');
            }
            if (Schema::hasColumn('bookings', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });
    }
};
