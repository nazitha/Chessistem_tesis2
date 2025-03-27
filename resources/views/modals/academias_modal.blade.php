<div class="modal fade" id="modal_academias" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="title_academias">Nuevo usuario</h1>
      </div>
      <div class="modal-body">
        <form action="" id="form_academias" class="needs-validation" novalidate>
            <div class="input-group mb-3">
              <span class="input-group-text">Academia:</span>
              <input id="input_nombre_academia" type="text" class="form-control" aria-label="Campo para nombre de la academia" autocomplete="off" required>
              <div class="invalid-feedback">
                Por favor, ingrese el nombre de la academia
              </div>
            </div>

            <div class="input-group mb-3">
              <span class="input-group-text">Correo:</span>
              <input id="input_correo_academia" type="email" class="form-control" aria-label="Campo para correo electrónico de la academia" autocomplete="off">
            </div>

            <div class="input-group mb-3">
              <span class="input-group-text">Teléfono:</span>
              <input id="input_phone_academia" type="tel" class="form-control" aria-label="Campo para telefono de la academia" autocomplete="off">
            </div>

            <div class="input-group mb-3">
              <span class="input-group-text">Director:</span>
              <input id="input_director_academia" type="text" class="form-control" aria-label="Campo para nombre del director de la academia" autocomplete="off" required>
              <div class="invalid-feedback">
                Por favor, ingrese el nombre del director o representante de la academia
              </div>
            </div>

            <div class="input-group mb-3">
              <div class="input-group">
                <span class="input-group-text">Dirección:</span>
                <textarea class="form-control" aria-label="With textarea" id="input_direccion_academia"></textarea>
              </div>
            </div>

            <div class="input-group mb-3">
                <label class="input-group-text" for="select_ciudades_academia">Ciudad:</label>
                <select class="form-select" id="select_ciudades_academia" required>
                  <option selected>Choose...</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
                <div class="invalid-feedback">
                  Por favor, seleccione una ciudad
                </div>
            </div>

            <div class="form-check form-switch" style="margin-bottom: 3%; display: flex; justify-content: flex-end;">
              <input class="form-check-input" type="checkbox" role="switch" id="switch_estado_academia" 
                style="background-color: #28a745; border-color: #28a745; border-radius: 50px; position: relative;" 
                onchange="cambiarColorSwitch_academia(this)" checked>
              <label class="form-check-label" for="switch_estado_academia" id="switchLabel_academia" style="position: relative; margin-left: 2%;">
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
  function cambiarColorSwitch_academia(checkbox) 
  {
    const label = document.getElementById("switchLabel_academia");

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
      label.textContent = "Inactivo";
    }
  }
</script>