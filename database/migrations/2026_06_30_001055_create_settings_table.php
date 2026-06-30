<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('hospital_name')->default('RSUD Merauke');
            $table->date('target_date')->nullable();
            $table->boolean('is_pendidikan')->default(false);
            $table->boolean('prognas_full')->default(true);
            $table->string('calc_mode')->default('bobot');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
