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
        Schema::create('room_pictures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();

            $table->string('disk')->default('public');
            $table->string('path');

            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();     
            $table->unsignedInteger('width')->nullable();   
            $table->unsignedInteger('height')->nullable();  

            $table->boolean('is_cover')->default(false); 
            $table->unsignedSmallInteger('sort_order')->default(0); 
            $table->string('caption')->nullable(); 

            $table->timestamps();

            $table->index(['room_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_pictures');
    }
};
