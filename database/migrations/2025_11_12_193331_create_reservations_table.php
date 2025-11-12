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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->date('check_in');
            $table->date('check_out');
            $table->enum('status', ['pending','confirmed','canceled'])->default('pending');
            $table->enum('payment_method', ['credit_card','cash','pix'])->nullable();
            $table->timestamps();
            
            $table->unique(['room_id','check_in','check_out','status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
