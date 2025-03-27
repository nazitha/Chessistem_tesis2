<div class="modal fade" id="modal_torneos" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="title_torneos">Nuevo usuario</h1>
      </div>
      <div class="modal-body">
        <form action="" id="form_torneos" class="needs-validation" novalidate>
            <div class="input-group mb-3">
              <span class="input-group-text">Torneo:</span>
              <input id="input_nombre_torneo" type="text" class="form-control" aria-label="Campo para el nombre del torneo" autocomplete="off" required>
              <div class="invalid-feedback">
                Por favor, ingrese un nombre para el torneo
              </div>
            </div>

            <div class="input-group mb-3">
              <span class="input-group-text">Fecha:</span>
              <input id="input_fecha_torneo" type="date" class="form-control" aria-label="Campo para la fecha del torneo" autocomplete="off" required>
              <div class="invalid-feedback">
                Por favor, ingrese la fecha del torneo
              </div>
            </div>

            <div class="input-group mb-3">
              <span class="input-group-text">Hora:</span>
              <input id="input_hora_torneo" type="time" class="form-control" aria-label="Campo para la hora del torneo" autocomplete="off" required>
              <div class="invalid-feedback">
                Por favor, ingrese la hora del torneo
              </div>
            </div>

            <div class="input-group mb-3">
              <label class="input-group-text" for="select_categoria_torneos">Seleccione una categoría:</label>
              <select class="form-select" id="select_categoria_torneos" required>
                <option selected>Choose...</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
              <div class="invalid-feedback">
                Por favor, seleccione una categoría para el torneo
              </div>
            </div>

            <div class="input-group mb-3">
              <label class="input-group-text" for="select_formato_torneos">Seleccione un formato:</label>
              <select class="form-select" id="select_formato_torneos">
                <option value="" selected disabled>Seleccione un formato...</option>
                <option value="1" disabled>(No hay registros para mostrar)</option>
              </select>
            </div>

            <div class="input-group mb-3">
              <label class="input-group-text" for="select_emparejamiento_torneos">Sistemas de emparejamiento:</label>
              <select class="form-select" id="select_emparejamiento_torneos" required>
                <option value="" selected disabled>Sistemas de emparejamiento...</option>
                <option value="1" disabled>(No hay registros para mostrar)</option>
              </select>
              <div class="invalid-feedback">
                Por favor, seleccione un sistema de emparejamiento
              </div>
            </div>

            <div class="input-group mb-3">
              <span class="input-group-text">Lugar:</span>
              <input id="input_lugar_torneo" type="text" class="form-control" aria-label="Campo para el lugar del torneo" autocomplete="off" required>
              <div class="invalid-feedback">
                Por favor, ingrese el lugar del torneo
              </div>
            </div>

            <div class="input-group mb-3">
              <span class="input-group-text">No. rondas:</span>
              <input 
                id="input_rondas_torneo" 
                type="text"
                class="form-control" 
                pattern="\d*"
                aria-label="Campo para las rondas del torneo" 
                autocomplete="off" 
                required
              >
              <div class="invalid-feedback">
                Por favor, ingrese la cantidad de rondas
              </div>
            </div>

            <div class="input-group mb-3">
              <label class="input-group-text" for="select_federacion_torneos">Seleccione una federación:</label>
              <select class="form-select" id="select_federacion_torneos">
                <option selected>Choose...</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
            </div>

            <div class="input-group mb-3">
              <label class="input-group-text" for="select_organizador_torneos">Seleccione un organizador:</label>
              <select class="form-select" id="select_organizador_torneos" required>
                <option selected>Choose...</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
              <div class="invalid-feedback">
                Por favor, seleccione un organizador
              </div>
            </div>

            <div class="input-group mb-3">
              <label class="input-group-text" for="select_director_torneos">Seleccione un director:</label>
              <select class="form-select" id="select_director_torneos" required>
                <option selected>Choose...</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
              <div class="invalid-feedback">
                Por favor, seleccione un director
              </div>
            </div>

            <div class="input-group mb-3">
              <label class="input-group-text" for="select_arbitro_torneos">Seleccione un árbitro:</label>
              <select class="form-select" id="select_arbitro_torneos" required>
                <option selected>Choose...</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
              <div class="invalid-feedback">
                Por favor, seleccione un árbitro
              </div>
            </div>

            <div class="input-group mb-3">
              <label class="input-group-text" for="select_arbitrop_torneos">Seleccione un árbitro principal:</label>
              <select class="form-select" id="select_arbitrop_torneos" required>
                <option selected>Choose...</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
              <div class="invalid-feedback">
                Por favor, seleccione un árbitro principal
              </div>
            </div>

            <div class="input-group mb-3">
              <label class="input-group-text" for="select_arbitroadj_torneos">Seleccione un árbitro adjunto:</label>
              <select class="form-select" id="select_arbitroadj_torneos" required>
                <option selected>Choose...</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
              <div class="invalid-feedback">
                Por favor, seleccione un árbitro adjunto
              </div>
            </div>

            <div class="form-check form-switch" style="margin-bottom: 3%; display: flex; justify-content: flex-end;">
              <input class="form-check-input" type="checkbox" role="switch" id="switch_estado_torneo" 
                style="background-color: #28a745; border-color: #28a745; border-radius: 50px; position: relative;" 
                onchange="cambiarColorSwitch_torneo(this)" checked>
              <label class="form-check-label" for="switch_estado_torneo" id="switchLabel_torneo" style="position: relative; margin-left: 2%;">
                Activo
              </label>
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

<script>
  function cambiarColorSwitch_torneo(checkbox) 
  {
    const label = document.getElementById("switchLabel_torneo");

    if (checkbox.checked) 
    {
      checkbox.style.backgroundColor = '#28a745';
      checkbox.style.borderColor = '#28a745';
      label.textContent = "Activo";
    } 
    else 
    {
      checkbox.style.backgroundColor = '#dc3545';
      checkbox.style.borderColor = '#dc3545';
      label.textContent = "Finalizado";
    }
  }
</script>

<script>
  const inputRondas = document.getElementById('input_rondas_torneo');

  inputRondas.addEventListener('keypress', function(e) {
    const charCode = e.which || e.keyCode;
        
    if (charCode < 48 || charCode > 57) {
      e.preventDefault();
    }
  });

  inputRondas.addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '');
        
    if (this.value.length > 1) {
      this.value = this.value.replace(/^0+/, '');
    }
  });

  inputRondas.addEventListener('drop', function(e) {
    e.preventDefault();
  });
</script>