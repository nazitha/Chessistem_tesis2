@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10">
    <div class="flex justify-start mb-4">
        <a href="{{ route('miembros.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-sm font-medium rounded-md text-gray-700 border border-gray-300 hover:bg-gray-50">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold mb-8 text-gray-800">Editar Miembro</h2>
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form id="form-editar-miembro" method="POST" action="{{ route('miembros.update', $miembro) }}" class="needs-validation" novalidate>
            @csrf
            @method('PUT')
            
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
                        <label for="cedula" class="form-label fw-bold fs-6">Cédula</label>
                        <input type="text" name="cedula" id="cedula" value="{{ old('cedula', $miembro->cedula) }}" 
                               class="form-control form-control-sm fs-6 bg-white" required readonly>
                    </div>

                    <!-- Nombres y Apellidos -->
                    <div class="row g-2">
                        <!-- Nombres -->
                        <div class="col-md-6">
                            <label for="nombres" class="form-label fw-bold fs-6">Nombres <span class="text-danger">*</span></label>
                            <input type="text" name="nombres" id="nombres" value="{{ old('nombres', $miembro->nombres) }}" 
                                   class="form-control form-control-sm fs-6 bg-white @error('nombres') is-invalid @enderror" required>
                            @error('nombres')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">Los nombres son requeridos</div>
                            @enderror
                        </div>

                        <!-- Apellidos -->
                        <div class="col-md-6">
                            <label for="apellidos" class="form-label fw-bold fs-6">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" name="apellidos" id="apellidos" value="{{ old('apellidos', $miembro->apellidos) }}" 
                                   class="form-control form-control-sm fs-6 bg-white @error('apellidos') is-invalid @enderror" required>
                            @error('apellidos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">Los apellidos son requeridos</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Sexo -->
                    <div class="mt-2">
                        <label for="sexo" class="form-label fw-bold fs-6">Sexo <span class="text-danger">*</span></label>
                        <select name="sexo" id="sexo" class="form-select form-select-sm fs-6 bg-white @error('sexo') is-invalid @enderror" required>
                            <option value="M" @if(old('sexo', $miembro->sexo) == 'M') selected @endif>Masculino</option>
                            <option value="F" @if(old('sexo', $miembro->sexo) == 'F') selected @endif>Femenino</option>
                        </select>
                        @error('sexo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="invalid-feedback">El sexo es requerido</div>
                        @enderror
                    </div>

                    <!-- Fecha de nacimiento -->
                    <div class="mt-2">
                        <label for="fecha_nacimiento" class="form-label fw-bold fs-6">Fecha de nacimiento <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" 
                               value="{{ old('fecha_nacimiento', $miembro->fecha_nacimiento ? \Carbon\Carbon::parse($miembro->fecha_nacimiento)->format('Y-m-d') : '') }}" 
                               class="form-control form-control-sm fs-6 bg-white @error('fecha_nacimiento') is-invalid @enderror" required>
                        @error('fecha_nacimiento')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="invalid-feedback">La fecha de nacimiento es requerida</div>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div class="mt-2">
                        <label for="telefono" class="form-label fw-bold fs-6">Teléfono <span class="text-danger">*</span></label>
                        <input type="tel" name="telefono" id="telefono" value="{{ old('telefono', $miembro->telefono) }}" 
                               class="form-control form-control-sm fs-6 bg-white @error('telefono') is-invalid @enderror" 
                               pattern="^\+?[0-9 ]{0,13}$" maxlength="14" required>
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="invalid-feedback">El teléfono es requerido</div>
                        @enderror
                    </div>

                    <!-- Estado -->
                    <div class="mt-2">
                        <div class="d-flex align-items-center space-x-3">
                            <label class="form-label fw-bold fs-6 mb-0">Estado</label>
                            <input type="hidden" name="estado_miembro" value="0">
                            <input type="checkbox" name="estado_miembro" value="1" 
                                   {{ old('estado_miembro', $miembro->estado_miembro) == '1' ? 'checked' : '' }}
                                   class="hidden" id="switch_miembro">
                            <button type="button" id="switch_button_miembro" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 {{ old('estado_miembro', $miembro->estado_miembro) == '1' ? 'bg-green-500' : 'bg-red-500' }}" aria-pressed="true">
                                <span class="sr-only">Estado del miembro</span>
                                <span id="switch_thumb_miembro" class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow-sm transition-transform duration-200 {{ old('estado_miembro', $miembro->estado_miembro) == '1' ? 'translate-x-4' : 'translate-x-0' }}"></span>
                            </button>
                            <span id="switchLabel_miembro" class="text-sm font-medium {{ old('estado_miembro', $miembro->estado_miembro) == '1' ? 'text-green-600' : 'text-red-600' }}">{{ old('estado_miembro', $miembro->estado_miembro) == '1' ? 'Activo' : 'Inactivo' }}</span>
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
                            <label for="academia_id" class="form-label fw-bold fs-6">Academia <span class="text-danger">*</span></label>
                            <select name="academia_id" id="academia_id" class="form-select form-select-sm fs-6 bg-white @error('academia_id') is-invalid @enderror" required>
                                <option value="">-</option>
                                @foreach($academias as $academia)
                                    <option value="{{ $academia->id_academia }}" @if(old('academia_id', $miembro->academia_id) == $academia->id_academia) selected @endif>{{ $academia->nombre_academia }}</option>
                                @endforeach
                            </select>
                            @error('academia_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">La academia es requerida</div>
                            @enderror
                        </div>

                        <!-- Fecha de inscripción -->
                        <div class="col-md-6">
                            <label for="fecha_inscripcion" class="form-label fw-bold fs-6">Fecha de inscripción <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_inscripcion" id="fecha_inscripcion" 
                                   value="{{ old('fecha_inscripcion', $miembro->fecha_inscripcion ? \Carbon\Carbon::parse($miembro->fecha_inscripcion)->format('Y-m-d') : '') }}" 
                                   class="form-control form-control-sm fs-6 bg-white @error('fecha_inscripcion') is-invalid @enderror" required>
                            @error('fecha_inscripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback">La fecha de inscripción es requerida</div>
                            @enderror
                        </div>
                    </div>

                    <!-- ELO -->
                    <div class="mt-2">
                        <label for="elo" class="form-label fw-bold fs-6">ELO</label>
                        <input type="text" name="elo" id="elo" value="{{ old('elo', $miembro->elo) }}" 
                               class="form-control form-control-sm fs-6 bg-white" 
                               placeholder="Ej: 1500" maxlength="4">
                    </div>

                    <!-- Correo -->
                    <div class="mt-2">
                        <label for="correo_sistema_id" class="form-label fw-bold fs-6">Correo del sistema</label>
                        <select name="correo_sistema_id" id="correo_sistema_id" class="form-select form-select-sm fs-6 bg-white">
                            <option value="">Sin correo asignado</option>
                            @foreach(App\Models\User::active()->get() as $user)
                                <option value="{{ $user->correo }}" @if(old('correo_sistema_id', $miembro->correo_sistema_id) == $user->correo) selected @endif>
                                    {{ $user->correo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="mt-8 flex justify-end">
                <a href="{{ route('miembros.show', $miembro) }}" class="mr-4 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancelar</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-semibold">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad del switch de estado (igual que usuarios)
    const switchButton = document.getElementById('switch_button_miembro');
    const hiddenCheckbox = document.getElementById('switch_miembro');
    
    switchButton.addEventListener('click', function() {
        const isActive = hiddenCheckbox.checked;
        hiddenCheckbox.checked = !isActive;
        
        if (hiddenCheckbox.checked) {
            // Estado Activo
            switchButton.classList.remove('bg-red-500');
            switchButton.classList.add('bg-green-500');
            document.getElementById('switch_thumb_miembro').classList.remove('translate-x-0');
            document.getElementById('switch_thumb_miembro').classList.add('translate-x-4');
            document.getElementById('switchLabel_miembro').textContent = 'Activo';
            document.getElementById('switchLabel_miembro').classList.remove('text-red-600');
            document.getElementById('switchLabel_miembro').classList.add('text-green-600');
        } else {
            // Estado Inactivo
            switchButton.classList.remove('bg-green-500');
            switchButton.classList.add('bg-red-500');
            document.getElementById('switch_thumb_miembro').classList.remove('translate-x-4');
            document.getElementById('switch_thumb_miembro').classList.add('translate-x-0');
            document.getElementById('switchLabel_miembro').textContent = 'Inactivo';
            document.getElementById('switchLabel_miembro').classList.remove('text-green-600');
            document.getElementById('switchLabel_miembro').classList.add('text-red-600');
        }
    });
    
    // Validación del input ELO - solo números enteros
    const eloInput = document.getElementById('elo');
    
    eloInput.addEventListener('input', function(e) {
        // Remover cualquier carácter que no sea número
        let value = e.target.value.replace(/[^0-9]/g, '');
        
        // Limitar a 4 dígitos máximo
        if (value.length > 4) {
            value = value.substring(0, 4);
        }
        
        // Actualizar el valor del input
        e.target.value = value;
    });
    
    // Prevenir pegar texto que contenga caracteres no numéricos
    eloInput.addEventListener('paste', function(e) {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text');
        const numbersOnly = paste.replace(/[^0-9]/g, '').substring(0, 4);
        e.target.value = numbersOnly;
    });
    
    // Prevenir teclas que no sean números, backspace, delete, tab, escape, enter
    eloInput.addEventListener('keydown', function(e) {
        const allowedKeys = [8, 9, 27, 13, 46]; // backspace, tab, escape, enter, delete
        const isNumber = (e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105);
        
        if (!allowedKeys.includes(e.keyCode) && !isNumber) {
            e.preventDefault();
        }
    });
    
    // Bootstrap validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });

            // Validación del campo teléfono
            const telefonoInput = document.getElementById('telefono');
            if (telefonoInput) {
                telefonoInput.addEventListener('input', function(e) {
                    let value = e.target.value;
                    
                    // Remover caracteres no permitidos excepto + al inicio
                    if (value.startsWith('+')) {
                        // Si empieza con +, mantenerlo y procesar el resto
                        let rest = value.substring(1);
                        rest = rest.replace(/[^0-9 ]/g, '');
                        
                        // Validar espacios: máximo 2 y no pueden ir juntos
                        let spaces = (rest.match(/ /g) || []).length;
                        if (spaces > 2) {
                            // Si hay más de 2 espacios, remover los últimos
                            rest = rest.split(' ').slice(0, 3).join(' ');
                        }
                        
                        // Remover espacios consecutivos
                        rest = rest.replace(/  +/g, ' ');
                        
                        e.target.value = '+' + rest;
                    } else {
                        // Si no empieza con +, solo permitir números y espacios
                        value = value.replace(/[^0-9 ]/g, '');
                        
                        // Validar espacios: máximo 2 y no pueden ir juntos
                        let spaces = (value.match(/ /g) || []).length;
                        if (spaces > 2) {
                            value = value.split(' ').slice(0, 3).join(' ');
                        }
                        
                        // Remover espacios consecutivos
                        value = value.replace(/  +/g, ' ');
                        
                        e.target.value = value;
                    }
                    
                    // Limitar longitud total
                    if (e.target.value.length > 14) {
                        e.target.value = e.target.value.substring(0, 14);
                    }
                });
                
                // Prevenir pegar contenido inválido
                telefonoInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    let paste = (e.clipboardData || window.clipboardData).getData('text');
                    
                    // Limpiar el contenido pegado
                    let cleaned = paste.replace(/[^0-9+ ]/g, '');
                    if (cleaned.startsWith('+')) {
                        let rest = cleaned.substring(1).replace(/[^0-9 ]/g, '');
                        let spaces = (rest.match(/ /g) || []).length;
                        if (spaces > 2) {
                            rest = rest.split(' ').slice(0, 3).join(' ');
                        }
                        rest = rest.replace(/  +/g, ' ');
                        this.value = '+' + rest;
                    } else {
                        cleaned = cleaned.replace(/[^0-9 ]/g, '');
                        let spaces = (cleaned.match(/ /g) || []).length;
                        if (spaces > 2) {
                            cleaned = cleaned.split(' ').slice(0, 3).join(' ');
                        }
                        cleaned = cleaned.replace(/  +/g, ' ');
                        this.value = cleaned;
                    }
                    
                    if (this.value.length > 14) {
                        this.value = this.value.substring(0, 14);
                    }
                });
            }
        }, false);
    })();
});
</script>
@endsection 