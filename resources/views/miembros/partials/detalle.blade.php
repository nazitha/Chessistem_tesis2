<div class="p-6">
    <h2 class="text-xl font-bold mb-4">Detalle del Miembro</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <span class="font-semibold">Cédula:</span>
            <span>{{ $miembro->cedula }}</span>
        </div>
        <div>
            <span class="font-semibold">Nombres:</span>
            <span>{{ $miembro->nombres }}</span>
        </div>
        <div>
            <span class="font-semibold">Apellidos:</span>
            <span>{{ $miembro->apellidos }}</span>
        </div>
        <div>
            <span class="font-semibold">Sexo:</span>
            <span>{{ $miembro->sexo == 'M' ? 'Masculino' : 'Femenino' }}</span>
        </div>
        <div>
            <span class="font-semibold">Fecha de nacimiento:</span>
            <span>{{ \Carbon\Carbon::parse($miembro->fecha_nacimiento)->format('d-m-Y') }}</span>
        </div>
        <div>
            <span class="font-semibold">Fecha de inscripción:</span>
            <span>{{ \Carbon\Carbon::parse($miembro->fecha_inscripcion)->format('d-m-Y') }}</span>
        </div>
        <div>
            <span class="font-semibold">Estado:</span>
            <span>
                @if($miembro->estado_miembro)
                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Activo</span>
                @else
                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Inactivo</span>
                @endif
            </span>
        </div>
        <div>
            <span class="font-semibold">Academia:</span>
            <span>{{ $miembro->academia->nombre_academia ?? '-' }}</span>
        </div>
        <div>
            <span class="font-semibold">Correo:</span>
            <span>{{ $miembro->correo_sistema_id ?? '-' }}</span>
        </div>
    </div>
</div> 