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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price');
            $table->unsignedTinyInteger('rating_avg')->default(0);
            $table->timestamps();
        });

        Schema::create('room_specialty', function (Blueprint $table) {
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('specialty_id')->constrained()->cascadeOnDelete();
            $table->primary(['room_id','specialty_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('room_specialty');
    }
};
