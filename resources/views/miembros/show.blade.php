@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-10">
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('miembros.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-sm font-medium rounded-md text-gray-700 border border-gray-300 hover:bg-gray-50">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
        <a href="{{ route('miembros.edit', $miembro) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar Miembro
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold mb-8 text-gray-800">Detalle del Miembro</h2>
        
        <!-- Card Datos Personales -->
        <div class="card mb-4">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 fw-bold fs-5 d-flex align-items-center">
                    <svg class="w-5 h-5 me-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Datos Personales
                </h6>
            </div>
            <div class="card-body py-3">
                <!-- Cédula -->
                <div class="mb-2">
                    <label class="form-label fw-bold fs-6">Cédula</label>
                    <div class="form-control form-control-sm fs-6 bg-white">{{ $miembro->cedula }}</div>
                </div>

                <!-- Nombres y Apellidos -->
                <div class="row g-2">
                    <!-- Nombres -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold fs-6">Nombres</label>
                        <div class="form-control form-control-sm fs-6 bg-white">{{ $miembro->nombres }}</div>
                    </div>

                    <!-- Apellidos -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold fs-6">Apellidos</label>
                        <div class="form-control form-control-sm fs-6 bg-white">{{ $miembro->apellidos }}</div>
                    </div>
                </div>

                <!-- Sexo -->
                <div class="mt-2">
                    <label class="form-label fw-bold fs-6">Sexo</label>
                    <div class="form-control form-control-sm fs-6 bg-white">{{ $miembro->sexo == 'M' ? 'Masculino' : 'Femenino' }}</div>
                </div>

                <!-- Fecha de nacimiento -->
                <div class="mt-2">
                    <label class="form-label fw-bold fs-6">Fecha de nacimiento</label>
                    <div class="form-control form-control-sm fs-6 bg-white">{{ \Carbon\Carbon::parse($miembro->fecha_nacimiento)->format('d-m-Y') }}</div>
                </div>

                <!-- Teléfono -->
                <div class="mt-2">
                    <label class="form-label fw-bold fs-6">Teléfono</label>
                    <div class="form-control form-control-sm fs-6 bg-white">{{ $miembro->telefono ?? 'No especificado' }}</div>
                </div>

                <!-- Estado -->
                <div class="mt-2">
                    <label class="form-label fw-bold fs-6">Estado</label>
                    <div class="d-flex align-items-center">
                        <div class="position-relative">
                            <div class="rounded-pill {{ $miembro->estado_miembro ? 'bg-success' : 'bg-danger' }}" style="width: 40px; height: 20px;"></div>
                            <div class="position-absolute bg-white rounded-circle shadow" style="width: 16px; height: 16px; top: 2px; {{ $miembro->estado_miembro ? 'left: 22px;' : 'left: 2px;' }}"></div>
                        </div>
                        <span class="ms-2 fw-medium {{ $miembro->estado_miembro ? 'text-success' : 'text-danger' }}">
                            {{ $miembro->estado_miembro ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Datos Académicos -->
        <div class="card mb-4">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 fw-bold fs-5 d-flex align-items-center">
                    <svg class="w-5 h-5 me-2 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                    Datos Académicos
                </h6>
            </div>
            <div class="card-body py-3">
                <div class="row g-2">
                    <!-- Academia -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold fs-6">Academia</label>
                        <div class="form-control form-control-sm fs-6 bg-white">{{ $miembro->academia->nombre_academia ?? '-' }}</div>
                    </div>

                    <!-- Fecha de inscripción -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold fs-6">Fecha de inscripción</label>
                        <div class="form-control form-control-sm fs-6 bg-white">{{ \Carbon\Carbon::parse($miembro->fecha_inscripcion)->format('d-m-Y') }}</div>
                    </div>
                </div>

                <!-- ELO -->
                <div class="mt-2">
                    <label class="form-label fw-bold fs-6">ELO</label>
                    <div class="form-control form-control-sm fs-6 bg-white">{{ $miembro->elo ?? '-' }}</div>
                </div>

                <!-- Correo -->
                <div class="mt-2">
                    <label class="form-label fw-bold fs-6">Correo del sistema</label>
                    <div class="form-control form-control-sm fs-6 bg-white">{{ $miembro->correo_sistema_id ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 