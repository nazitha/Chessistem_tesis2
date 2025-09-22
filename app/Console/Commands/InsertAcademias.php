<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Academia;
use Illuminate\Support\Facades\DB;

class InsertAcademias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'academias:insert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insertar 20 academias de ajedrez en la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Insertando 20 academias de ajedrez...');

        $academias = [
            [
                'nombre_academia' => 'Academia de Ajedrez Managua',
                'correo_academia' => 'info@academiamanagua.com',
                'telefono_academia' => '2222-1111',
                'representante_academia' => 'Carlos Mendoza',
                'direccion_academia' => 'Km 4.5 Carretera Masaya, Managua',
                'ciudad_id' => 12, // Managua
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Club de Ajedrez León',
                'correo_academia' => 'contacto@clubleon.com',
                'telefono_academia' => '2311-2222',
                'representante_academia' => 'María González',
                'direccion_academia' => 'Centro Histórico, León',
                'ciudad_id' => 10, // León
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Escuela de Ajedrez Granada',
                'correo_academia' => 'escuela@granadaajedrez.com',
                'telefono_academia' => '2552-3333',
                'representante_academia' => 'Roberto Silva',
                'direccion_academia' => 'Calle La Calzada, Granada',
                'ciudad_id' => 8, // Granada
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Academia Masaya Chess',
                'correo_academia' => 'info@masayachess.com',
                'telefono_academia' => '2522-4444',
                'representante_academia' => 'Ana López',
                'direccion_academia' => 'Parque Central, Masaya',
                'ciudad_id' => 13, // Masaya
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Club de Ajedrez Estelí',
                'correo_academia' => 'club@esteliajedrez.com',
                'telefono_academia' => '2713-5555',
                'representante_academia' => 'José Ramírez',
                'direccion_academia' => 'Barrio San Juan, Estelí',
                'ciudad_id' => 7, // Estelí
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Escuela de Ajedrez Matagalpa',
                'correo_academia' => 'escuela@matagalpaajedrez.com',
                'telefono_academia' => '2772-6666',
                'representante_academia' => 'Carmen Herrera',
                'direccion_academia' => 'Centro de la ciudad, Matagalpa',
                'ciudad_id' => 14, // Matagalpa
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Academia de Ajedrez Jinotega',
                'correo_academia' => 'academia@jinotegaajedrez.com',
                'telefono_academia' => '2782-7777',
                'representante_academia' => 'Pedro Martínez',
                'direccion_academia' => 'Barrio Central, Jinotega',
                'ciudad_id' => 9, // Jinotega
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Club de Ajedrez Chinandega',
                'correo_academia' => 'club@chinandegaajedrez.com',
                'telefono_academia' => '2341-8888',
                'representante_academia' => 'Laura Castro',
                'direccion_academia' => 'Parque Central, Chinandega',
                'ciudad_id' => 3, // Chinandega
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Escuela de Ajedrez Boaco',
                'correo_academia' => 'escuela@boacoajedrez.com',
                'telefono_academia' => '2542-9999',
                'representante_academia' => 'Miguel Torres',
                'direccion_academia' => 'Centro Histórico, Boaco',
                'ciudad_id' => 1, // Boaco
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Academia de Ajedrez Juigalpa',
                'correo_academia' => 'academia@juigalpaajedrez.com',
                'telefono_academia' => '2512-1010',
                'representante_academia' => 'Sofía Morales',
                'direccion_academia' => 'Barrio El Centro, Juigalpa',
                'ciudad_id' => 4, // Juigalpa
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Club de Ajedrez Rivas',
                'correo_academia' => 'club@rivasajedrez.com',
                'telefono_academia' => '2533-2020',
                'representante_academia' => 'Diego Jiménez',
                'direccion_academia' => 'Parque Central, Rivas',
                'ciudad_id' => 17, // Rivas
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Escuela de Ajedrez Somoto',
                'correo_academia' => 'escuela@somotoajedrez.com',
                'telefono_academia' => '2722-3030',
                'representante_academia' => 'Elena Vargas',
                'direccion_academia' => 'Centro de la ciudad, Somoto',
                'ciudad_id' => 11, // Somoto
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Academia de Ajedrez Ocotal',
                'correo_academia' => 'academia@ocotalajedrez.com',
                'telefono_academia' => '2732-4040',
                'representante_academia' => 'Carlos Ruiz',
                'direccion_academia' => 'Barrio Central, Ocotal',
                'ciudad_id' => 15, // Ocotal
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Club de Ajedrez Jinotepe',
                'correo_academia' => 'club@jinotepeajedrez.com',
                'telefono_academia' => '2533-5050',
                'representante_academia' => 'María Elena Flores',
                'direccion_academia' => 'Parque Central, Jinotepe',
                'ciudad_id' => 2, // Jinotepe
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Escuela de Ajedrez Puerto Cabezas',
                'correo_academia' => 'escuela@puertocabezasajedrez.com',
                'telefono_academia' => '2792-6060',
                'representante_academia' => 'Roberto Brown',
                'direccion_academia' => 'Centro de la ciudad, Puerto Cabezas',
                'ciudad_id' => 5, // Puerto Cabezas
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Academia de Ajedrez Bluefields',
                'correo_academia' => 'academia@bluefieldsajedrez.com',
                'telefono_academia' => '2572-7070',
                'representante_academia' => 'Linda Campbell',
                'direccion_academia' => 'Barrio Central, Bluefields',
                'ciudad_id' => 6, // Bluefields
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Club de Ajedrez San Carlos',
                'correo_academia' => 'club@sancarlosajedrez.com',
                'telefono_academia' => '2583-8080',
                'representante_academia' => 'Francisco Gutiérrez',
                'direccion_academia' => 'Parque Central, San Carlos',
                'ciudad_id' => 16, // San Carlos
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Escuela de Ajedrez Managua Centro',
                'correo_academia' => 'centro@managuaajedrez.com',
                'telefono_academia' => '2222-9090',
                'representante_academia' => 'Patricia Sandoval',
                'direccion_academia' => 'Barrio Martha Quezada, Managua',
                'ciudad_id' => 12, // Managua
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Academia de Ajedrez León Norte',
                'correo_academia' => 'norte@leonajedrez.com',
                'telefono_academia' => '2311-1111',
                'representante_academia' => 'Alberto Méndez',
                'direccion_academia' => 'Barrio San Felipe, León',
                'ciudad_id' => 10, // León
                'estado_academia' => true
            ],
            [
                'nombre_academia' => 'Club de Ajedrez Granada Colonial',
                'correo_academia' => 'colonial@granadaajedrez.com',
                'telefono_academia' => '2552-2222',
                'representante_academia' => 'Isabel Rivas',
                'direccion_academia' => 'Calle Atravesada, Granada',
                'ciudad_id' => 8, // Granada
                'estado_academia' => true
            ]
        ];

        try {
            DB::beginTransaction();

            foreach ($academias as $academia) {
                Academia::create($academia);
                $this->line("✓ Insertada: {$academia['nombre_academia']}");
            }

            DB::commit();
            $this->info('¡Éxito! Se insertaron 20 academias de ajedrez.');
            $this->info('Total de academias en la base de datos: ' . Academia::count());

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error al insertar academias: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
