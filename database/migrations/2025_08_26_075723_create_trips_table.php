<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
        {
            Schema::create('trips', function (Blueprint $table) {
                $table->id();
                $table->string('origin');
                $table->string('destination');
                $table->dateTime('departure_time');
                $table->dateTime('arrival_time');
                $table->integer('capacity');
                $table->decimal('price', 8, 2);
                $table->timestamps();
            });
        }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
