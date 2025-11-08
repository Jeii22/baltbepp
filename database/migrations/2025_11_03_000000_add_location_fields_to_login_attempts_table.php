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
        Schema::table('login_attempts', function (Blueprint $table) {
            $table->string('country')->nullable()->after('ip_address');
            $table->string('region')->nullable()->after('country');
            $table->string('city')->nullable()->after('region');
            $table->decimal('latitude', 10, 7)->nullable()->after('city');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login_attempts', function (Blueprint $table) {
            $table->dropColumn([
                'country',
                'region',
                'city',
                'latitude',
                'longitude',
            ]);
        });
    }
};
