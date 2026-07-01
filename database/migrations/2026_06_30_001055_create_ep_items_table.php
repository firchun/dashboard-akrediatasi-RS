<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ep_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pokja_id')->constrained()->cascadeOnDelete();
            $table->foreignId('standar_id')->constrained()->cascadeOnDelete();
            $table->string('no_urut')->nullable();
            $table->text('uraian')->nullable();
            $table->boolean('bukti_r')->default(false);
            $table->boolean('bukti_d')->default(false);
            $table->boolean('bukti_o')->default(false);
            $table->boolean('bukti_w')->default(false);
            $table->boolean('bukti_s')->default(false);
            $table->string('nilai')->default('');
            $table->text('fakta_analisis')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->text('pengingat')->nullable();
            $table->string('pic')->nullable();
            $table->text('link')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ep_items');
    }
};
