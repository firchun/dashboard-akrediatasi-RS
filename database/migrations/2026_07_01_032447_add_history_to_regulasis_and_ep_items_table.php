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
        Schema::table('regulasis', function (Blueprint $table) {
            $table->json('history')->nullable()->after('link');
        });
        
        Schema::table('ep_items', function (Blueprint $table) {
            $table->json('history')->nullable()->after('link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regulasis', function (Blueprint $table) {
            $table->dropColumn('history');
        });
        
        Schema::table('ep_items', function (Blueprint $table) {
            $table->dropColumn('history');
        });
    }
};
