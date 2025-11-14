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
        Schema::table('rooms', function (Blueprint $table) {
            $table->foreignId('specialty_id')
              ->after('city_id')
              ->nullable() 
              ->constrained()
              ->cascadeOnDelete();
        });

        DB::table('rooms')->update(['specialty_id' => 1]);

        Schema::table('rooms', function (Blueprint $table) {
            $table->foreignId('specialty_id')
                ->nullable(false)
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['specialty_id']);
            $table->dropColumn('specialty_id');
        });
    }
};
