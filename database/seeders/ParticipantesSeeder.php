<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParticipantesSeeder extends Seeder
{
    public function run(): void
    {
        $participantes = [
            // Variaciones de Ian Eiffel Sevilla Castillo
            [
                'cedula' => 'V12345678',
                'nombres' => 'Ian Eiffel',
                'apellidos' => 'Sevilla Castillo',
                'sexo' => 'M',
                'fecha_nacimiento' => '1990-01-01',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => null
            ],
            [
                'cedula' => 'V12345679',
                'nombres' => 'Ian',
                'apellidos' => 'Sevilla Castillo',
                'sexo' => 'M',
                'fecha_nacimiento' => '1991-02-15',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => null
            ],
            [
                'cedula' => 'V12345680',
                'nombres' => 'Eiffel',
                'apellidos' => 'Sevilla Castillo',
                'sexo' => 'M',
                'fecha_nacimiento' => '1992-03-20',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => null
            ],
            [
                'cedula' => 'V12345681',
                'nombres' => 'Ian Eiffel',
                'apellidos' => 'Sevilla',
                'sexo' => 'M',
                'fecha_nacimiento' => '1993-04-25',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => null
            ],
            [
                'cedula' => 'V12345682',
                'nombres' => 'Ian Eiffel',
                'apellidos' => 'Castillo',
                'sexo' => 'M',
                'fecha_nacimiento' => '1994-05-30',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => null
            ],
            [
                'cedula' => 'V12345683',
                'nombres' => 'Eiffel Ian',
                'apellidos' => 'Sevilla Castillo',
                'sexo' => 'M',
                'fecha_nacimiento' => '1995-06-10',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => null
            ],
            [
                'cedula' => 'V12345684',
                'nombres' => 'Ian',
                'apellidos' => 'Castillo Sevilla',
                'sexo' => 'M',
                'fecha_nacimiento' => '1996-07-15',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => null
            ],
            [
                'cedula' => 'V12345685',
                'nombres' => 'Eiffel',
                'apellidos' => 'Castillo Sevilla',
                'sexo' => 'M',
                'fecha_nacimiento' => '1997-08-20',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => null
            ],
            // Invented participants
            [
                'cedula' => 'V22345678',
                'nombres' => 'Carlos',
                'apellidos' => 'González Pérez',
                'sexo' => 'M',
                'fecha_nacimiento' => '1992-05-15',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => null
            ],
            [
                'cedula' => 'V22345679',
                'nombres' => 'María',
                'apellidos' => 'Rodríguez López',
                'sexo' => 'F',
                'fecha_nacimiento' => '1993-08-20',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => null
            ],
            [
                'cedula' => 'V22345680',
                'nombres' => 'José',
                'apellidos' => 'Martínez Sánchez',
                'sexo' => 'M',
                'fecha_nacimiento' => '1991-03-10',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => null
            ],
            [
                'cedula' => 'V22345681',
                'nombres' => 'Ana',
                'apellidos' => 'Hernández García',
                'sexo' => 'F',
                'fecha_nacimiento' => '1994-11-25',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => null
            ],
            [
                'cedula' => 'V22345682',
                'nombres' => 'Pedro',
                'apellidos' => 'López Torres',
                'sexo' => 'M',
                'fecha_nacimiento' => '1990-07-30',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => null
            ],
            [
                'cedula' => 'V22345683',
                'nombres' => 'Laura',
                'apellidos' => 'García Pérez',
                'sexo' => 'F',
                'fecha_nacimiento' => '1993-02-14',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => null
            ],
            [
                'cedula' => 'V22345684',
                'nombres' => 'Juan',
                'apellidos' => 'Sánchez Rodríguez',
                'sexo' => 'M',
                'fecha_nacimiento' => '1992-09-05',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => null
            ],
            [
                'cedula' => 'V22345685',
                'nombres' => 'Sofía',
                'apellidos' => 'Torres López',
                'sexo' => 'F',
                'fecha_nacimiento' => '1991-12-18',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => null
            ]
        ];

        DB::table('miembros')->insert($participantes);
    }
} 