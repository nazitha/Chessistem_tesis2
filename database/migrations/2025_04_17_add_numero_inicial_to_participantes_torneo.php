<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participantes_torneo', function (Blueprint $table) {
            $table->integer('numero_inicial')->nullable()->after('miembro_id');
        });
    }

    public function down(): void
    {
        Schema::table('participantes_torneo', function (Blueprint $table) {
            $table->dropColumn('numero_inicial');
        });
    }
}; 