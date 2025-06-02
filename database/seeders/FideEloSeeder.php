<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FideEloSeeder extends Seeder
{
    public function run(): void
    {
        // Crear país 'Nacional' si no existe
        $pais = DB::table('paises')->where('nombre_pais', 'Nacional')->first();
        if (!$pais) {
            $pais_id = DB::table('paises')->insertGetId([
                'nombre_pais' => 'Nacional'
            ]);
        } else {
            $pais_id = $pais->id_pais;
        }

        // Crear federación NCA si no existe
        if (!DB::table('federaciones')->where('acronimo', 'NCA')->exists()) {
            DB::table('federaciones')->insert([
                'acronimo' => 'NCA',
                'nombre_federacion' => 'Federación Nacional',
                'pais_id' => $pais_id,
                'federacion_estado' => true
            ]);
        }

        // Crear categoría de ELO STD si no existe
        $eloCategoria = DB::table('elo_categorias')->where('categoria_elo', 'STD')->first();
        if (!$eloCategoria) {
            $eloCategoriaId = DB::table('elo_categorias')->insertGetId([
                'categoria_elo' => 'STD',
                'descripcion' => 'ELO estándar',
                'elo_categorias_estado' => true
            ]);
        } else {
            $eloCategoriaId = $eloCategoria->no_elo;
        }

        // Para cada miembro, crear FIDE y ELO
        $miembros = DB::table('miembros')->get();
        foreach ($miembros as $miembro) {
            $fide_id = 'FIDE' . substr($miembro->cedula, 1); // Ejemplo: FIDE12345678
            // Insertar FIDE
            DB::table('fides')->insert([
                'fide_id' => $fide_id,
                'cedula_ajedrecista_id' => $miembro->cedula,
                'fed_id' => 'NCA',
                'titulo' => null,
                'fide_estado' => true
            ]);
            // Insertar ELO
            DB::table('puntajes_elo')->insert([
                'fide_id_miembro' => $fide_id,
                'no_categoria_elo' => $eloCategoriaId,
                'elo' => rand(1200, 2400)
            ]);
        }
    }
} 