<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    public $timestamps = true;
    
    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'rol_id', 'id');
    }

    public function permisos()
    {
        return $this->belongsToMany(Permission::class, 'asignaciones_permisos', 'rol_id', 'permiso_id');
    }
}