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
        Schema::table('partidas', function (Blueprint $table) {
            $table->integer('ronda_torneo_id')->nullable()->after('ronda');
            $table->foreign('ronda_torneo_id')
                  ->references('id_torneo')
                  ->on('torneos')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partidas', function (Blueprint $table) {
            $table->dropForeign(['ronda_torneo_id']);
            $table->dropColumn('ronda_torneo_id');
        });
    }
}; 