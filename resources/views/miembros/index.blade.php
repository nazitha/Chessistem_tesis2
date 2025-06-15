@extends('layouts.app')

@section('content')
<div id='contenedor-miembros' class="p-8 mt-6 lg:mt-0 rounded shadow bg-white mt-2">
    <h2 class="text-2xl font-bold mb-4">Lista de Miembros</h2>
    <table id="tabla-miembros" class="stripe hover" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
        <thead>
            <tr>
                <th>Cédula</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Academia</th>
                <th>Estado</th>
                <th>Sexo</th>
                <th>Fecha de nacimiento</th>
                <th>Ciudad</th>
                <th>Teléfono</th>
                <th>Inscripción</th>
                <th>Club</th>
            </tr>
        </thead>
        <tbody>
            @foreach($miembros as $miembro)
                <tr>
                    <td>{{ $miembro->cedula }}</td>
                    <td>{{ $miembro->nombres }}</td>
                    <td>{{ $miembro->apellidos }}</td>
                    <td>{{ $miembro->usuario->correo ?? '-' }}</td>
                    <td>{{ $miembro->usuario->rol->nombre ?? '-' }}</td>
                    <td>{{ $miembro->academia->nombre_academia ?? '-' }}</td>
                    <td>{{ $miembro->estado_miembro ? 'Activo' : 'Inactivo' }}</td>
                    <td>{{ $miembro->sexo == 'M' ? 'Masculino' : 'Femenino' }}</td>
                    <td>{{ $miembro->fecha_nacimiento ? $miembro->fecha_nacimiento->format('d-m-Y') : '-' }}</td>
                    <td>{{ $miembro->ciudad->nombre_ciudad ?? '-' }}</td>
                    <td>{{ $miembro->telefono ?? '-' }}</td>
                    <td>{{ $miembro->fecha_inscripcion ? $miembro->fecha_inscripcion->format('d-m-Y') : '-' }}</td>
                    <td>{{ $miembro->club ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 