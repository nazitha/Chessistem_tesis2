<div class="modal fade" id="modal_add_users" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content rounded-2xl shadow-2xl p-2">
      <div class="modal-header bg-gray-800 text-white rounded-t-2xl">
        <h1 class="modal-title fs-5 text-xl font-bold w-full text-center" id="title_add_users">Nuevo usuario</h1>
      </div>
      <div class="modal-body p-6">
        <form action="" id="form_add_users" class="needs-validation" novalidate>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-700 font-medium mb-1">Correo:</label>
              <input id="input_correo_add_user" type="email" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" autocomplete="off" required>
              <div class="invalid-feedback text-red-500 text-xs">Por favor, ingrese un correo electrónico</div>
            </div>
            <div>
              <label class="block text-gray-700 font-medium mb-1">Rol:</label>
              <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" id="select_rol_add_user">
                <option selected>Seleccione un rol...</option>
                <option value="1">Administrador</option>
                <option value="2">Usuario</option>
                <option value="3">Arbitro</option>
                <option value="4">Organizador</option>
              </select>
            </div>
            <div>
              <label class="block text-gray-700 font-medium mb-1">Contraseña:</label>
              <input id="input_pass_add_user" type="password" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" autocomplete="off" required>
              <div class="invalid-feedback text-red-500 text-xs">Por favor, ingrese su contraseña</div>
            </div>
            <div>
              <label class="block text-gray-700 font-medium mb-1">Nueva contraseña:</label>
              <input id="input_pass_edit_user" type="password" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" autocomplete="off">
            </div>
            <div>
              <label class="block text-gray-700 font-medium mb-1">Confirmar contraseña:</label>
              <input id="input_passconfirm_edit_user" type="password" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" autocomplete="off">
            </div>
            <div>
              <label class="block text-gray-700 font-medium mb-1">Estado:</label>
              <div class="flex items-center space-x-2">
                <input class="form-check-input" type="checkbox" role="switch" id="switch_add_user" onchange="cambiarColorSwitch(this)">
                <label class="form-check-label" for="switch_add_user" id="switchLabel">Inactivo</label>
              </div>
            </div>
          </div>
          <div class="flex justify-end mt-8 space-x-3">
            <button type="button" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold shadow">Agregar</button>
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