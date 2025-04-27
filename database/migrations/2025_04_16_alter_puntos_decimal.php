<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participantes_torneo', function (Blueprint $table) {
            $table->decimal('puntos', 8, 2)->default(0)->change();
        });
        Schema::table('participantes', function (Blueprint $table) {
            $table->decimal('puntos', 8, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('participantes_torneo', function (Blueprint $table) {
            $table->integer('puntos')->default(0)->change();
        });
        Schema::table('participantes', function (Blueprint $table) {
            $table->integer('puntos')->default(0)->change();
        });
    }
}; 