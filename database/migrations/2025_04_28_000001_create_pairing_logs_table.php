<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pairing_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('torneo_id')->constrained()->onDelete('cascade');
            $table->foreignId('ronda_id')->constrained('rondas_torneo')->onDelete('cascade');
            $table->foreignId('participante_id')->nullable()->constrained('participantes_torneo')->onDelete('cascade');
            $table->foreignId('equipo_id')->nullable()->constrained('equipos_torneo')->onDelete('cascade');
            $table->string('tipo_decision'); // 'emparejamiento', 'flotamiento', 'bye', 'color'
            $table->string('motivo');
            $table->json('detalles')->nullable(); // Para guardar información adicional
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pairing_logs');
    }
}; 