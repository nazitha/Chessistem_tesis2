<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EncryptPasswords extends Command
{
    protected $signature = 'encrypt:passwords';
    protected $description = 'Encripta las contraseñas existentes en la base de datos';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $usuarios = DB::table('usuarios')->get();

        foreach ($usuarios as $usuario) {
            if (!Hash::needsRehash($usuario->contrasena)) {
                continue; 
            }

            DB::table('usuarios')
                ->where('correo', $usuario->correo)
                ->update(['contrasena' => Hash::make($usuario->contrasena)]);
        }

        $this->info('Contraseñas encriptadas correctamente.');
    }
}
