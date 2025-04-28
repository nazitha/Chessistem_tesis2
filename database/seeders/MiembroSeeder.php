<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Miembro;
use App\Models\User;

class MiembroSeeder extends Seeder
{
    public function run(): void
    {
        $miembros = [
            [
                'cedula' => '001-290190-0001A',
                'nombres' => 'Juan Carlos',
                'apellidos' => 'Pérez González',
                'sexo' => 'M',
                'fecha_nacimiento' => '1990-01-29',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => 'juancarlos@estrellasdelajedrez.com'
            ],
            [
                'cedula' => '001-150585-0002B',
                'nombres' => 'María José',
                'apellidos' => 'Rodríguez López',
                'sexo' => 'F',
                'fecha_nacimiento' => '1985-05-15',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => 'mariajose@estrellasdelajedrez.com'
            ],
            [
                'cedula' => '001-100880-0003C',
                'nombres' => 'Roberto Antonio',
                'apellidos' => 'García Martínez',
                'sexo' => 'M',
                'fecha_nacimiento' => '1980-08-10',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => 'roberto@estrellasdelajedrez.com'
            ],
            [
                'cedula' => '001-200475-0004D',
                'nombres' => 'Ana Patricia',
                'apellidos' => 'Morales Ruiz',
                'sexo' => 'F',
                'fecha_nacimiento' => '1975-04-20',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => 'ana@estrellasdelajedrez.com'
            ],
            [
                'cedula' => '001-120392-0005E',
                'nombres' => 'Carlos Eduardo',
                'apellidos' => 'Sánchez Flores',
                'sexo' => 'M',
                'fecha_nacimiento' => '1992-03-12',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => 'carlos@estrellasdelajedrez.com'
            ],
            [
                'cedula' => '001-050688-0006F',
                'nombres' => 'Laura Isabel',
                'apellidos' => 'Torres Mendoza',
                'sexo' => 'F',
                'fecha_nacimiento' => '1988-06-05',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => 'laura@estrellasdelajedrez.com'
            ],
            [
                'cedula' => '001-230195-0007G',
                'nombres' => 'Miguel Ángel',
                'apellidos' => 'Ramírez Castro',
                'sexo' => 'M',
                'fecha_nacimiento' => '1995-01-23',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => 'miguel@estrellasdelajedrez.com'
            ],
            [
                'cedula' => '001-180487-0008H',
                'nombres' => 'Patricia Elena',
                'apellidos' => 'Díaz Vargas',
                'sexo' => 'F',
                'fecha_nacimiento' => '1987-04-18',
                'fecha_inscripcion' => now(),
                'estado_miembro' => true,
                'correo_sistema_id' => 'patricia@estrellasdelajedrez.com'
            ]
        ];

        // Primero crear los usuarios
        foreach ($miembros as $miembro) {
            User::firstOrCreate(
                ['correo' => $miembro['correo_sistema_id']],
                [
                    'contrasena' => bcrypt('password123'),
                    'rol_id' => 2, // Rol de usuario regular
                    'usuario_estado' => true
                ]
            );
        }

        // Luego crear los miembros
        foreach ($miembros as $miembro) {
            Miembro::firstOrCreate(
                ['cedula' => $miembro['cedula']],
                $miembro
            );
        }
    }
} 