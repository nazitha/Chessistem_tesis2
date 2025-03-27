<div class="modal fade" id="modal_depto" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="title_add_depto">Agregar país</h1>
      </div>
      <div class="modal-body">
        <form action="" id="form_add_depto" class="needs-validation" novalidate>
            <div class="input-group mb-3">
                <label class="input-group-text" for="select_paises_depto">Paises:</label>
                <select class="form-select" id="select_paises_depto" required>
                  <option selected>Choose...</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
                <div class="invalid-feedback">
                  Por favor, seleccione un país
                </div>
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text">Departamento:</span>
                <input id="input_nuevo_depto" type="text" class="form-control" aria-label="Campo para el departamento a agregar" autocomplete="off" required>
                <div class="invalid-feedback">
                  Por favor, ingrese el nombre del departamento a agregar
                </div>
            </div>

            <div class="modal-footer">
              <button type="button" id='nuevo_pais_depto'
                  style="background-color: #1e2936; color: white; border: 1px solid transparent; 
                  padding: 0.375rem 0.75rem; font-size: 1rem; font-weight: 400; line-height: 1.5; 
                  border-radius: 0.25rem; text-align: center; vertical-align: middle; 
                  cursor: pointer; display: inline-block; transition: background-color 0.15s ease-in-out, 
                  border-color 0.15s ease-in-out; margin-right: 10px;">
                  ¿No aparece el país?
                </button>

                <button type="submit" style="background-color: #1e2936; color: white; border: 1px 
                  solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; 
                  font-weight: 400; line-height: 1.5; border-radius: 0.25rem; text-align: center; 
                  vertical-align: middle; cursor: pointer; display: inline-block; transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;"
                  >Agregar
                </button>

                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
            </div>

        </form>
      </div>
    </div>
  </div>
</div>