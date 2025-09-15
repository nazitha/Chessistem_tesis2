@extends('layouts.app')

@section('content')
@php
    use App\Helpers\PermissionHelper;
@endphp
<div class="max-w-4xl mx-auto mt-10">
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('academias.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-sm font-medium rounded-md text-gray-700 border border-gray-300 hover:bg-gray-50">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
        @if(PermissionHelper::canUpdate('academias'))
            <a href="{{ route('academias.edit', $academia->id_academia) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar Academia
            </a>
        @endif
    </div>
    
    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold mb-8 text-gray-800">Detalle de la Academia</h2>
        
        <!-- Card Generalidades -->
        <div class="card mb-4">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 fw-bold fs-5 d-flex align-items-center">
                    <svg class="w-5 h-5 me-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Generalidades
                </h6>
            </div>
            <div class="card-body py-3">
                <div class="row g-2">
                    <!-- Nombre -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold fs-6">Nombre</label>
                        <div class="form-control form-control-sm fs-6 bg-light">{{ $academia->nombre_academia }}</div>
                    </div>

                    <!-- Representante -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold fs-6">Representante</label>
                        <div class="form-control form-control-sm fs-6 bg-light">{{ $academia->representante_academia }}</div>
                    </div>
                </div>

                <!-- Ciudad -->
                <div class="mt-2">
                    <label class="form-label fw-bold fs-6">Ciudad</label>
                    <div class="form-control form-control-sm fs-6 bg-light">
                        {{ $academia->ciudad ? $academia->ciudad->nombre_ciudad . ', ' . 
                           ($academia->ciudad->departamento->nombre_depto ?? '-') . ' (' . 
                           ($academia->ciudad->departamento->pais->nombre_pais ?? '-') . ')' : '-' }}
                    </div>
                </div>

                <!-- Dirección -->
                <div class="mt-2">
                    <label class="form-label fw-bold fs-6">Dirección</label>
                    <div class="form-control form-control-sm fs-6 bg-light">{{ $academia->direccion_academia }}</div>
                </div>

                <!-- Estado -->
                <div class="mt-2">
                    <label class="form-label fw-bold fs-6">Estado</label>
                    <div class="d-flex align-items-center">
                        <div class="position-relative">
                            <div class="rounded-pill {{ $academia->estado_academia ? 'bg-success' : 'bg-danger' }}" style="width: 40px; height: 20px;"></div>
                            <div class="position-absolute bg-white rounded-circle shadow" style="width: 16px; height: 16px; top: 2px; {{ $academia->estado_academia ? 'left: 22px;' : 'left: 2px;' }}"></div>
                        </div>
                        <span class="ms-2 fw-medium {{ $academia->estado_academia ? 'text-success' : 'text-danger' }}">
                            {{ $academia->estado_academia ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Contacto -->
        <div class="card mb-4">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 fw-bold fs-5 d-flex align-items-center">
                    <svg class="w-5 h-5 me-2 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Contacto
                </h6>
            </div>
            <div class="card-body py-3">
                <div class="row g-2">
                    <!-- Correo -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold fs-6">Correo</label>
                        <div class="form-control form-control-sm fs-6 bg-light">{{ $academia->correo_academia }}</div>
                    </div>

                    <!-- Teléfono -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold fs-6">Teléfono</label>
                        <div class="form-control form-control-sm fs-6 bg-light">{{ $academia->telefono_academia }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 