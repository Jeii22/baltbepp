<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('type')->nullable();
            $table->enum('gender', ['male','female','other'])->nullable();
            $table->date('birthdate')->nullable();
            $table->string('id_number')->nullable();
            $table->decimal('fare', 10, 2)->default(0);
            $table->string('seat')->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passengers');
    }
};