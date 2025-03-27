<div class="modal fade" data-bs-backdrop="static" id="modal_asigpermis" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="title_asigpermis">Asignar permiso</h1>
      </div>
      <div class="modal-body">
        <form action="" id="form_asigpermis" class="needs-validation" novalidate>

          <div class="input-group mb-3">
            <label class="input-group-text" for="select_rol_asigpermis">Rol:</label>
            <select class="form-select" id="select_rol_asigpermis">
              <option selected>Choose...</option>
              <option value="1">One</option>
              <option value="2">Two</option>
              <option value="3">Three</option>
            </select>
          </div>
            
          <div class="input-group mb-3">
            <label class="input-group-text" for="select_permiso_asigpermis">Permisos:</label>
            <select class="form-select" id="select_permiso_asigpermis">
              <option selected>Choose...</option>
              <option value="1">One</option>
              <option value="2">Two</option>
              <option value="3">Three</option>
            </select>
          </div>

            <div class="modal-footer">

                <button type="submit" id='submit_btn_asigpermis' style="background-color: #1e2936; color: white; border: 1px 
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