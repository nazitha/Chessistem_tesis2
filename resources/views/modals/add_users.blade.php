<!-- Modal para Nuevo Usuario  -->
<div id="modal_add_users" class="fixed inset-0 flex items-center justify-center z-50 hidden pointer-events-none">
    <!-- BACKDROP -->
    <div class="fixed inset-0 bg-black bg-opacity-50 z-40 pointer-events-auto"></div>
    <!-- MODAL -->
    <div class="mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white relative z-50 pointer-events-auto">
        <!-- Botón de cierre -->
        <button type="button" onclick="cerrarModalAddUsuario()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-2xl font-bold focus:outline-none close-modal">&times;</button>
        <div class="mt-3">
            <h2 class="text-2xl font-bold text-center mb-6">Nuevo Usuario</h2>
            <form id="form_add_users" class="needs-validation" novalidate>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Correo:</label>
                        <input id="input_correo_add_user" type="email" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" autocomplete="off" required>
                        <div class="invalid-feedback text-red-500 text-xs">Por favor, ingrese un correo electrónico</div>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Rol:</label>
                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" id="select_rol_add_user" required>
                            <option value="" selected>Seleccione un rol...</option>
                            <option value="1">Administrador</option>
                            <option value="2">Usuario</option>
                            <option value="3">Arbitro</option>
                            <option value="4">Organizador</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Estado:</label>
                        <select id="input_estado_add_user" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div></div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Contraseña:</label>
                        <input id="input_pass_add_user" type="password" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" autocomplete="off" required>
                        <div class="invalid-feedback text-red-500 text-xs">Por favor, ingrese su contraseña</div>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Confirmar contraseña:</label>
                        <input id="input_passconfirm_add_user" type="password" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" autocomplete="off" required>
                    </div>
                </div>
                <div class="flex justify-end mt-8 space-x-3">
                    <button type="button" onclick="cerrarModalAddUsuario()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 btn-cancelar">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Agregar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function cerrarModalAddUsuario() {
    document.getElementById('modal_add_users').classList.add('hidden');
    const form = document.getElementById('form_add_users');
    form.reset();
    form.classList.remove('was-validated');
    if (document.activeElement) document.activeElement.blur(); 
    eliminarBackdropsBootstrap();
}

document.addEventListener('DOMContentLoaded', function() {
    const btnNuevoUsuario = document.getElementById('btnNuevoUsuario');
    if (btnNuevoUsuario) {
        btnNuevoUsuario.addEventListener('click', function() {
            document.getElementById('modal_add_users').classList.remove('hidden');
        });
    }

    // Cerrar modal con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModalAddUsuario();
        }
    });

    // Manejar el envío del formulario
    const formAddUsers = document.getElementById('form_add_users');
    let isSubmitting = false;
    if (formAddUsers) {
        formAddUsers.onsubmit = function(e) {
            e.preventDefault();
            if (isSubmitting) return;
            isSubmitting = true;
            // Validar el formulario
            if (!formAddUsers.checkValidity()) {
                formAddUsers.classList.add('was-validated');
                isSubmitting = false;
                return;
            }
            // Obtener los datos del formulario
            const formData = {
                correo: document.getElementById('input_correo_add_user').value,
                contrasena: document.getElementById('input_pass_add_user').value,
                contrasena_confirmation: document.getElementById('input_passconfirm_add_user').value,
                rol_id: document.getElementById('select_rol_add_user').value,
                usuario_estado: document.getElementById('input_estado_add_user').value === "1"
            };
            // Validar que las contraseñas coincidan
            if (formData.contrasena !== formData.contrasena_confirmation) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Las contraseñas no coinciden',
                    text: 'Por favor, verifica que ambas contraseñas sean iguales.'
                });
                isSubmitting = false;
                return;
            }
            // Mostrar indicador de carga
            const submitBtn = formAddUsers.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Creando...';
            submitBtn.disabled = true;
            // Enviar la petición AJAX
            fetch('/usuarios', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(formData)
            })
            .then(async response => {
                let data;
                try {
                    data = await response.json();
                } catch (e) {
                    data = {};
                }
                if (response.ok && data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Usuario creado exitosamente',
                        text: 'El usuario ha sido registrado correctamente.'
                    }).then(() => {
                        cerrarModalAddUsuario();
                        submitBtn.blur();
                        isSubmitting = false;
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                    });
                } else if (data.errors) {
                    let errorMessage = '';
                    Object.keys(data.errors).forEach(key => {
                        errorMessage += `${data.errors[key][0]}<br>`;
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Errores de validación',
                        html: errorMessage
                    });
                    isSubmitting = false;
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error al crear el usuario'
                    });
                    isSubmitting = false;
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al crear el usuario'
                });
                isSubmitting = false;
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        };
    }
});
</script>
<script>
// Función para eliminar backdrops de Bootstrap
function eliminarBackdropsBootstrap() {
    var backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(function(backdrop) {
        backdrop.remove();
    });
    document.body.classList.remove('modal-open');
}

// Eliminar backdrops al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    eliminarBackdropsBootstrap();
    
    // También eliminar cada 500ms por si se recrea
    setInterval(eliminarBackdropsBootstrap, 500);
});

// Eliminar cuando se abre el modal
document.addEventListener('DOMContentLoaded', function() {
    const btnNuevoUsuario = document.getElementById('btnNuevoUsuario');
    if (btnNuevoUsuario) {
        btnNuevoUsuario.addEventListener('click', function() {
            document.getElementById('modal_add_users').classList.remove('hidden');
            // Eliminar backdrop inmediatamente al abrir
            setTimeout(eliminarBackdropsBootstrap, 10);
        });
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
</script>