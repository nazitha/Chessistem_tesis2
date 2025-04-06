<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_email'; 
    public $incrementing = true; // Desactiva auto-incremento
    protected $keyType = 'int'; // Tipo de clave primaria

    protected $fillable = [
        'correo',
        'contrasena',
        'rol_id',
        'usuario_estado'
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    public function getAuthIdentifierName()
    {
        return 'correo';
    }
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

       protected $casts = [
        'usuario_estado' => 'boolean',
        'fecha_creacion' => 'datetime', // Si tienes este campo
    ];

    public function scopeActive($query)
    {
       return $query->where('usuario_estado', true);
    }

    public function scopeUnlinked($query)
    {
       return $query->doesntHave('miembro');
    }   

    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }
       
    public function permissions()
    {
        return $this->hasManyThrough(
            Permission::class,
            Role::class,
            'id', // Foreign key on roles table
            'id', // Foreign key on permissions table
            'rol_id', // Local key on users table
            'id' // Local key on roles table
        );
    }
}

