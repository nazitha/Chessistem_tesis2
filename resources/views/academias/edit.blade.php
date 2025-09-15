@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto mt-10">
    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('academias.index')}}" class="inline-flex items-center px-4 py-2 bg-white text-sm font-medium rounded-md text-gray-700 border border-gray-300 hover:bg-gray-50">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold mb-8 text-gray-800">Editar Academia</h2>

        @if($errors->any())
            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('academias.update', $academia->id_academia) }}" method="POST">
            @csrf
            @method('PUT')
            
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
                            <label for="nombre_academia" class="form-label fw-bold fs-6">Nombre</label>
                            <input type="text" name="nombre_academia" id="nombre_academia" 
                                   value="{{ old('nombre_academia', $academia->nombre_academia) }}"
                                   class="form-control form-control-sm fs-6" required>
                        </div>

                        <!-- Representante -->
                        <div class="col-md-6">
                            <label for="representante_academia" class="form-label fw-bold fs-6">Representante</label>
                            <input type="text" name="representante_academia" id="representante_academia" 
                                   value="{{ old('representante_academia', $academia->representante_academia) }}"
                                   class="form-control form-control-sm fs-6" required>
                        </div>
                    </div>

                    <!-- Ciudad -->
                    <div class="mt-2">
                        <label for="ciudad_id" class="form-label fw-bold fs-6">Ciudad</label>
                        <select name="ciudad_id" id="ciudad_id" class="form-select form-select-sm fs-6" required>
                            <option value="">Seleccione una ciudad</option>
                            @foreach($ciudades as $ciudad)
                                <option value="{{ $ciudad->id_ciudad }}" 
                                    {{ old('ciudad_id', $academia->ciudad_id) == $ciudad->id_ciudad ? 'selected' : '' }}>
                                    {{ $ciudad->nombre_ciudad }}, {{ $ciudad->departamento->nombre_depto ?? '-' }} ({{ $ciudad->departamento->pais->nombre_pais ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dirección -->
                    <div class="mt-2">
                        <label for="direccion_academia" class="form-label fw-bold fs-6">Dirección</label>
                        <input type="text" name="direccion_academia" id="direccion_academia" 
                               value="{{ old('direccion_academia', $academia->direccion_academia) }}"
                               class="form-control form-control-sm fs-6" required>
                    </div>

                    <!-- Estado -->
                    <div class="mt-2">
                        <label class="form-label fw-bold fs-6">Estado</label>
                        <div class="d-flex align-items-center">
                            <label class="form-check-label d-flex align-items-center cursor-pointer">
                                <input type="checkbox" name="estado_academia" value="1" 
                                       {{ old('estado_academia', $academia->estado_academia) == '1' ? 'checked' : '' }}
                                       class="form-check-input d-none" id="estado_switch">
                                <div class="position-relative">
                                    <div class="bg-secondary rounded-pill" style="width: 40px; height: 20px;" id="switch-bg"></div>
                                    <div class="position-absolute bg-white rounded-circle shadow" style="width: 16px; height: 16px; top: 2px; left: 2px;" id="switch-knob"></div>
                                </div>
                                <span class="ms-2 fw-medium" id="estado-text">Activo</span>
                            </label>
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
                            <label for="correo_academia" class="form-label fw-bold fs-6">Correo</label>
                            <input type="email" name="correo_academia" id="correo_academia" 
                                   value="{{ old('correo_academia', $academia->correo_academia) }}"
                                   class="form-control form-control-sm fs-6" required>
                        </div>

                        <!-- Teléfono -->
                        <div class="col-md-6">
                            <label for="telefono_academia" class="form-label fw-bold fs-6">Teléfono</label>
                            <input type="text" name="telefono_academia" id="telefono_academia" 
                                   value="{{ old('telefono_academia', $academia->telefono_academia) }}"
                                   class="form-control form-control-sm fs-6" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded hover:bg-blue-600 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Actualizar Academia
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad del switch de estado
    const estadoSwitch = document.getElementById('estado_switch');
    const switchBg = document.getElementById('switch-bg');
    const switchKnob = document.getElementById('switch-knob');
    const estadoText = document.getElementById('estado-text');
    
    // Función para actualizar el estado visual del switch
    function updateSwitchState() {
        if (estadoSwitch.checked) {
            // Activo - Verde
            switchBg.classList.remove('bg-secondary');
            switchBg.classList.add('bg-success');
            switchKnob.style.transform = 'translateX(20px)';
            estadoText.textContent = 'Activo';
            estadoText.classList.remove('text-danger');
            estadoText.classList.add('text-success');
            estadoSwitch.value = '1';
        } else {
            // Inactivo - Rojo
            switchBg.classList.remove('bg-success');
            switchBg.classList.add('bg-danger');
            switchKnob.style.transform = 'translateX(0px)';
            estadoText.textContent = 'Inactivo';
            estadoText.classList.remove('text-success');
            estadoText.classList.add('text-danger');
            estadoSwitch.value = '0';
        }
    }
    
    // Event listener para el cambio del switch
    estadoSwitch.addEventListener('change', updateSwitchState);
    
    // Inicializar el estado visual del switch
    updateSwitchState();
});
</script>
@endsection 