<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

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
     * Set the password attribute.
     */
    public function setContrasenaAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['contrasena'] = Hash::make($value);
        }
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
        return Hash::check($plain, $this->contrasena);
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
        return $this->belongsTo(Role::class, 'rol_id', 'id');
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

    public function hasRole($role)
    {
        return $this->rol_id == $role;
    }

    public function miembro()
    {
        return $this->hasOne(Miembro::class, 'correo_sistema_id', 'correo');
    }
}

