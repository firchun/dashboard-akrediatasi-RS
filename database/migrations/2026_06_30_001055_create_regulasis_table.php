<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regulasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pokja_id')->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->string('jenis');
            $table->string('pic')->nullable();
            $table->date('target')->nullable();
            $table->text('link')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['Belum', 'Proses', 'Review', 'Selesai'])->default('Belum');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regulasis');
    }
};
