<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_email';
    public $incrementing = true;
    protected $keyType = 'int';

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

    protected $casts = [
        'usuario_estado' => 'boolean',
    ];

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'id_email';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    /**
     * Validate the user's credentials.
     */
    public function validateCredentials(array $credentials)
    {
        $plain = $credentials['password'] ?? $credentials['contrasena'] ?? null;
        if (!$plain) {
            return false;
        }
        return $this->contrasena === $plain;
    }

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

