<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    public $timestamps = false;
    
    protected $fillable = ['nombre', 'descripcion'];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'rol_id');
    }

    public function permisos()
    {
        return $this->belongsToMany(Permission::class, 'asignaciones_permisos', 'rol_id', 'permiso_id');
    }
}