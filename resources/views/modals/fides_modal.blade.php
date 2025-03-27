<div class="modal fade" id="modal_fides" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="title_fides">Nuevo usuario</h1>
      </div>
      <div class="modal-body">
        <form action="" id="form_fides" class="needs-validation" novalidate>
            <div class="input-group mb-3">
              <span class="input-group-text">FIDE-ID:</span>
              <input id="input_id_fide" type="int" class="form-control" aria-label="Campo para el ID FIDE" autocomplete="off" required>
              <div class="invalid-feedback">
                Por favor, ingrese el código FIDE del jugador
              </div>
            </div>

            <div class="input-group mb-3">
              <label class="input-group-text" for="select_identificacion_fide">Identificación:</label>
              <select class="form-select" id="select_identificacion_fide" required>
                <option selected>Choose...</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
              <div class="invalid-feedback">
                Por favor, seleccione el número de identificación del jugador
              </div>
            </div>

            <div class="input-group mb-3">
              <label class="input-group-text" for="select_federacion_fide">Federación:</label>
              <select class="form-select" id="select_federacion_fide" required>
                <option selected>Choose...</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
              <div class="invalid-feedback">
                Por favor, seleccione la federación del jugador
              </div>
            </div>

            <div class="input-group mb-3">
              <label class="input-group-text" for="select_titulo_fide">Título (opcional):</label>
              <select class="form-select" id="select_titulo_fide">
                <option selected disabled>Seleccione un título...</option>
                <option disabled>(No hay registros para mostrar)</option>
              </select>
            </div>

            <div class="input-group mb-3">
              <span class="input-group-text">ELO Blitz:</span>
              <input id="input_blitz_fide" type="text" class="form-control" aria-label="Campo para el puntaje ELO en categoría Blitz" autocomplete="off" 
                value="0" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');" onfocus="if (this.value === '0') this.value = '';"
                onblur="if (this.value === '') this.value = '0';">
            </div>

            <div class="input-group mb-3">
              <span class="input-group-text">ELO Clásico:</span>
              <input id="input_clasico_fide" type="text" class="form-control" aria-label="Campo para el puntaje ELO en categoría Clásico" autocomplete="off" 
                value="0" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');" onfocus="if (this.value === '0') this.value = '';"
                onblur="if (this.value === '') this.value = '0';">
            </div>

            <div class="input-group mb-3">
              <span class="input-group-text">ELO Rápido:</span>
              <input id="input_rapido_fide" type="text" class="form-control" aria-label="Campo para el puntaje ELO en categoría Rápido" autocomplete="off" 
                value="0" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');" onfocus="if (this.value === '0') this.value = '';"
                onblur="if (this.value === '') this.value = '0';">
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
