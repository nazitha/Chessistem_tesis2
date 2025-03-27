<div class="modal fade" id="modal_add_users" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="title_add_users">Nuevo usuario</h1>
      </div>
      <div class="modal-body">
        <form action="" id="form_add_users" class="needs-validation" novalidate>
            <div class="input-group mb-3">
              <span class="input-group-text">Correo:</span>
              <input id="input_correo_add_user" type="email" class="form-control" aria-label="Campo para correo electrónico" autocomplete="off" required>
              <div class="invalid-feedback">
                Por favor, ingrese un correo electrónico
              </div>
            </div>

            <div class="input-group mb-3" id='div_setpass'>
              <span class="input-group-text">Contraseña:</span>
              <input id="input_pass_add_user" type="password" class="form-control" aria-label="Campo para contraseña" autocomplete="off" required>
              <div class="invalid-feedback">
                Por favor, ingrese su contraseña
              </div>
            </div>

            <div class="input-group mb-3" id='div_newpass_edit'>
              <span class="input-group-text">Nueva contraseña:</span>
              <input id="input_pass_edit_user" type="password" class="form-control" aria-label="Campo para contraseña" autocomplete="off">
            </div>

            <div class="input-group mb-3" id='div_confirmpass_edit'>
              <span class="input-group-text">Confirmar contraseña:</span>
              <input id="input_passconfirm_edit_user" type="password" class="form-control" aria-label="Campo para contraseña" autocomplete="off">
            </div>

            <div class="input-group mb-3">
                <label class="input-group-text" for="select_rol_add_user">Seleccione un rol:</label>
                <select class="form-select" id="select_rol_add_user">
                  <option selected>Choose...</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
            </div>
            
            <div class="form-check form-switch" style="margin-bottom: 3%; display: flex; justify-content: flex-end;">
                <input class="form-check-input" type="checkbox" role="switch" id="switch_add_user" 
                    style="background-color: #dc3545; border-color: #dc3545; border-radius: 50px; position: relative;" 
                    onchange="cambiarColorSwitch(this)">
                <label class="form-check-label" for="flexSwitchCheckDefault" id="switchLabel" style="position: relative; margin-left: 2%;">
                    Inactivo
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
  function cambiarColorSwitch(checkbox) 
  {
    const label = document.getElementById("switchLabel");

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