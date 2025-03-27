<div class="modal fade" id="modal_partidasbusqueda" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="title_partidasbusqueda">Nuevo usuario</h1>
      </div>
      <div class="modal-body">
        <form action="" id="form_partidasbusqueda" class="needs-validation" novalidate>
          <div class="input-group mb-3">
            <label class="input-group-text" for="select_torneo_partida">Torneo:</label>
            <select class="form-select" id="select_torneo_partida" required>
              <option selected>Choose...</option>
              <option value="1">One</option>
              <option value="2">Two</option>
              <option value="3">Three</option>
            </select>
            <div class="invalid-feedback">
              Por favor, seleccione un torneo a emparejar
            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" style="background-color: #1e2936; color: white; border: 1px 
              solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; 
              font-weight: 400; line-height: 1.5; border-radius: 0.25rem; text-align: center; 
              vertical-align: middle; cursor: pointer; display: inline-block; transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;"
              >Agregar</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>