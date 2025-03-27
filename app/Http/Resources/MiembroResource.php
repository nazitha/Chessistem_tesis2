<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MiembroResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'cedula' => $this->cedula,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'correo_sistema_id' => $this->usuario->correo ?? null,
            'rol' => $this->usuario->rol->nombre ?? null,
            'academia_id' => $this->academia->nombre_academia ?? null,
            'estado_miembro' => $this->estado_miembro ? 'Activo' : 'Inactivo',
            'sexo' => $this->formatted_sexo,
            'fecha_nacimiento' => $this->fecha_nacimiento->format('d-m-Y'),
            'ciudad_id' => $this->formatted_ciudad,
            'telefono' => $this->telefono,
            'fecha_inscripcion' => $this->fecha_inscripcion->format('d-m-Y'),
            'club' => $this->club
        ];
    }
}