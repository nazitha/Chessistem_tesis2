<!-- Modal para asignar permisos -->
<div class="modal fade" id="asignarPermisosModal" tabindex="-1" role="dialog" aria-labelledby="asignarPermisosModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="asignarPermisosModalLabel">Asignar Permisos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formAsignarPermisos" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id">
                    
                    <div class="form-group">
                        <label for="rol_id">Rol:</label>
                        <select class="form-control" id="rol_id" name="rol_id" required>
                            <option value="">Seleccione un rol</option>
                            <option value="1">Administrador</option>
                            <option value="2">Evaluador</option>
                            <option value="3">Estudiante</option>
                            <option value="4">Gestor</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Permisos:</label>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="permiso_crear" name="permisos[]" value="crear">
                            <label class="custom-control-label" for="permiso_crear">Crear</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="permiso_editar" name="permisos[]" value="editar">
                            <label class="custom-control-label" for="permiso_editar">Editar</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="permiso_eliminar" name="permisos[]" value="eliminar">
                            <label class="custom-control-label" for="permiso_eliminar">Eliminar</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="permiso_ver" name="permisos[]" value="ver">
                            <label class="custom-control-label" for="permiso_ver">Ver</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarPermisos()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
function abrirModalPermisos(userId) {
    $('#user_id').val(userId);
    $('#asignarPermisosModal').modal('show');
}

function guardarPermisos() {
    const formData = new FormData(document.getElementById('formAsignarPermisos'));
    
    fetch('/asignar-permisos', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Permisos asignados correctamente');
            $('#asignarPermisosModal').modal('hide');
            // Recargar la tabla o actualizar la vista según sea necesario
            location.reload();
        } else {
            alert('Error al asignar permisos: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    });
}
</script> 