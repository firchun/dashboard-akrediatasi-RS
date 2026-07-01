<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pokja_id')->constrained()->cascadeOnDelete();
            $table->string('kode');
            $table->text('uraian')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standars');
    }
};
