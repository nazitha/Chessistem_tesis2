<!-- Modal para Nuevo usuario / Editar usuario -->
<div id="modal_add_users" class="fixed inset-0 flex items-center justify-center z-50 hidden pointer-events-none">
    <!-- BACKDROP -->
    <div class="fixed inset-0 bg-black bg-opacity-50 z-40 pointer-events-auto"></div>
    <!-- MODAL -->
    <div class="mx-auto p-0 border w-full max-w-lg shadow-xl rounded-lg bg-white relative z-50 pointer-events-auto" style="border-color: #e5e7eb;">
        <!-- Encabezado del modal con título -->
        <div class="px-6 py-4 rounded-t-lg" style="background-color: #282c34;">
            <h2 id="modal_user_title" class="text-xl font-bold text-white">Nuevo usuario</h2>
        </div>
        
        <form id="form_add_users" class="needs-validation p-6" novalidate>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-sm font-medium" style="color: #282c34;">Correo electrónico <span class="text-red-600" aria-hidden="true">*</span></label>
                    <input id="input_correo_add_user" type="email" class="w-full px-3 py-2 bg-white border rounded-md focus:outline-none focus:ring-1 focus:ring-gray-400 text-sm" style="border-color: #d1d5db;" autocomplete="off" required>
                    <div class="invalid-feedback text-xs text-red-500 mt-1">Por favor, ingrese un correo válido</div>
                </div>
                
                <div class="space-y-1">
                    <label class="block text-sm font-medium" style="color: #282c34;">Rol <span class="text-red-600" aria-hidden="true">*</span></label>
                    <select class="w-full px-3 py-2 bg-white border rounded-md focus:outline-none focus:ring-1 focus:ring-gray-400 text-sm" style="border-color: #d1d5db;" id="select_rol_add_user" required>
                        <option value="" selected disabled>Seleccione un rol</option>
                        <option value="1">Administrador</option>
                        <option value="2">Usuario</option>
                        <option value="3">Árbitro</option>
                        <option value="4">Organizador</option>
                    </select>
                    <div class="invalid-feedback text-xs text-red-500 mt-1">Por favor, seleccione un rol</div>
                </div>
                
                <div id="div_pass_add_user" class="space-y-1">
                    <label class="block text-sm font-medium" style="color: #282c34;">Contraseña <span class="text-red-600" aria-hidden="true">*</span></label>
                    <input id="input_pass_add_user" type="password" class="w-full px-3 py-2 bg-white border rounded-md focus:outline-none focus:ring-1 focus:ring-gray-400 text-sm" style="border-color: #d1d5db;" autocomplete="off" required minlength="8" maxlength="80">
                    <div class="invalid-feedback text-xs text-red-500 mt-1">La contraseña debe tener al menos 8 caracteres</div>
                </div>

                <div id="div_passconfirm_add_user" class="space-y-1">
                    <label class="block text-sm font-medium" style="color: #282c34;">Confirmar contraseña <span class="text-red-600" aria-hidden="true">*</span></label>
                    <input id="input_passconfirm_add_user" type="password" class="w-full px-3 py-2 bg-white border rounded-md focus:outline-none focus:ring-1 focus:ring-gray-400 text-sm" style="border-color: #d1d5db;" autocomplete="off" required minlength="8" maxlength="80">
                    <div class="invalid-feedback text-xs text-red-500 mt-1">Las contraseñas deben coincidir</div>
                </div>
                
                <!-- Estado - Switch mejorado -->
                <div class="md:col-span-2 pt-1">
                    <div class="flex items-center space-x-3">
                        <label class="text-sm font-medium" style="color: #282c34;">Estado</label>
                        <input type="checkbox" id="switch_add_user" class="hidden" checked>
                        <button type="button" id="switch_button" class="relative inline-flex h-5 w-9 items-center rounded-full bg-green-500 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400" aria-pressed="true">
                            <span class="sr-only">Estado del usuario</span>
                            <span id="switch_thumb" class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow-sm transition-transform duration-200 translate-x-4"></span>
                        </button>
                        <span id="switchLabel" class="text-sm font-medium text-green-600">Activo</span>
                    </div>
                </div>
            </div>
            
            <!-- Botones alineados como solicitado -->
            <div class="flex justify-end space-x-3 mt-4">
                <button type="submit" id="btn_submit_user" class="px-4 py-2 text-sm font-medium rounded-md text-white transition-colors" style="background-color: #282c34;">
                    Crear usuario
                </button>
                <button type="button" class="px-4 py-2 text-sm font-medium rounded-md text-white transition-colors btn-cancelar" style="background-color: #ef4444;">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Función para inicializar el switch
function initUserStatusSwitch() {
    const switchButton = document.getElementById('switch_button');
    const hiddenCheckbox = document.getElementById('switch_add_user');
    
    switchButton.addEventListener('click', function() {
        const isActive = hiddenCheckbox.checked;
        hiddenCheckbox.checked = !isActive;
        
        if (hiddenCheckbox.checked) {
            // Estado Activo
            switchButton.classList.remove('bg-red-500');
            switchButton.classList.add('bg-green-500');
            document.getElementById('switch_thumb').classList.remove('translate-x-0');
            document.getElementById('switch_thumb').classList.add('translate-x-4');
            document.getElementById('switchLabel').textContent = 'Activo';
            document.getElementById('switchLabel').classList.remove('text-red-600');
            document.getElementById('switchLabel').classList.add('text-green-600');
        } else {
            // Estado Inactivo
            switchButton.classList.remove('bg-green-500');
            switchButton.classList.add('bg-red-500');
            document.getElementById('switch_thumb').classList.remove('translate-x-4');
            document.getElementById('switch_thumb').classList.add('translate-x-0');
            document.getElementById('switchLabel').textContent = 'Inactivo';
            document.getElementById('switchLabel').classList.remove('text-green-600');
            document.getElementById('switchLabel').classList.add('text-red-600');
        }
    });
}

// Función para eliminar backdrops de Bootstrap
function eliminarBackdropsBootstrap() {
    var backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(function(backdrop) {
        backdrop.remove();
    });
    document.body.classList.remove('modal-open');
}

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar el switch
    initUserStatusSwitch();
    
    // Eliminar backdrops al cargar la página
    eliminarBackdropsBootstrap();
    
    // También eliminar cada 500ms por si se recrea
    setInterval(eliminarBackdropsBootstrap, 500);
    
    // Botón para abrir modal
    const btnNuevoUsuario = document.getElementById('btnNuevoUsuario');
    if (btnNuevoUsuario) {
        btnNuevoUsuario.addEventListener('click', function() {
            document.getElementById('modal_add_users').classList.remove('hidden');
            // Eliminar backdrop inmediatamente al abrir
            setTimeout(eliminarBackdropsBootstrap, 10);
        });
    }

    // Cerrar modal con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModalAddUsuario();
        }
    });

    // Validación básica del formulario
    const formAddUsers = document.getElementById('form_add_users');
    if (formAddUsers) {
        formAddUsers.addEventListener('submit', function(e) {
            if (!formAddUsers.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                formAddUsers.classList.add('was-validated');
            }
        }, false);
    }
});

// Función de cierre mejorada
function cerrarModalAddUsuario() {
    document.getElementById('modal_add_users').classList.add('hidden');
    const form = document.getElementById('form_add_users');
    form.reset();
    form.classList.remove('was-validated');
    if (document.activeElement) document.activeElement.blur(); 
    eliminarBackdropsBootstrap();
}

// Eventos para cerrar modal
document.querySelectorAll('#modal_add_users .btn-cancelar').forEach(btn => {
    btn.addEventListener('click', function() {
        cerrarModalAddUsuario();
    });
});
</script>