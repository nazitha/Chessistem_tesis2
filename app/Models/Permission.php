<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permisos';
    public $timestamps = false;

    protected $fillable = ['permiso'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'asignaciones_permisos', 'permiso_id', 'rol_id');
    }
}