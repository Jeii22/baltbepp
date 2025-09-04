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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->string('origin');
            $table->string('destination');
            $table->dateTime('departure_time');
            $table->unsignedInteger('adult');
            $table->unsignedInteger('child')->default(0);
            $table->unsignedInteger('infant')->default(0);
            $table->unsignedInteger('pwd')->default(0);
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->enum('status', ['pending','confirmed','cancelled'])->default('pending');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
