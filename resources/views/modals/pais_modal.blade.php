<!-- Modal de País -->
<div class="modal fade" id="paisModal" tabindex="-1" role="dialog" aria-labelledby="paisModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paisModalLabel">Gestión de País</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formPais">
                    @csrf
                    <div class="form-group">
                        <label for="nombre_pais">Nombre del País</label>
                        <input type="text" class="form-control" id="nombre_pais" name="nombre_pais" required>
                    </div>
                    <div class="form-group">
                        <label for="codigo_pais">Código del País (ISO)</label>
                        <input type="text" class="form-control" id="codigo_pais" name="codigo_pais" maxlength="2" required>
                        <small class="form-text text-muted">Código ISO de 2 letras (ej. ES, US, MX)</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="guardarPais()">Guardar</button>
            </div>
        </div>
    </div>
</div>