<div class="modal fade" id="modal_miembros" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="title_miembros">Nuevo usuario</h1>
      </div>
      <div class="modal-body">
        <form action="" id="form_miembros" class="needs-validation" novalidate>
            <div class="input-group mb-3">
              <span class="input-group-text">Identificación:</span>
              <input id="input_identificacion_miembro" type="text" class="form-control" aria-label="Campo para el numero de identificación" autocomplete="off" required>
              <div class="invalid-feedback">
                Por favor, ingrese la identificación del miembro
              </div>
            </div>

            <div class="input-group mb-3">
              <span class="input-group-text">Nombre:</span>
              <input id="input_nombre_miembros" type="text" class="form-control" aria-label="Campo para el nombre del mimebro" autocomplete="off" required>
              <div class="invalid-feedback">
                Por favor, ingrese el nombre del miembro
              </div>
            </div>

            <div class="input-group mb-3">
              <span class="input-group-text">Apellidos:</span>
              <input id="input_apellidos_miembros" type="text" class="form-control" aria-label="Campo para el apellido del mimebro" autocomplete="off" required>
              <div class="invalid-feedback">
                Por favor, ingrese el apellido del miembro
              </div>
            </div>

            <div class="input-group mb-3">
              <label class="input-group-text" for="select_sexo_miembros">Sexo:</label>
              <select class="form-select" id="select_sexo_miembros" required>
                <option value='' selected disabled>Seleccione un sexo...</option>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
              </select>
              <div class="invalid-feedback">
                Por favor, seleccione un sexo
              </div>
            </div>

            <div class="input-group mb-3">
              <label class="input-group-text" for="select_correos_miembros">Correo del sistema:</label>
              <select class="form-select" id="select_correos_miembros">
                <option opcion='' selected>Choose...</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
            </div>

            <div class="input-group mb-3">
              <label class="input-group-text" for="select_academia_miembros">Academia:</label>
              <select class="form-select" id="select_academia_miembros" required>
                <option opcion='' selected>Choose...</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
              <div class="invalid-feedback">
                Por favor, seleccione una academia
              </div>
            </div>

            <div class="input-group mb-3">
              <span class="input-group-text">Fecha de nacimiento:</span>
              <input id="input_fechanacimiento_miembros" type="date" class="form-control" aria-label="Campo para la fecha de nacimiento" autocomplete="off">
            </div>

            <div class="input-group mb-3">
              <label class="input-group-text" for="select_ciudad_miembros">Procedencia:</label>
              <select class="form-select" id="select_ciudad_miembros" required>
                <option value='' selected>Choose...</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
              <div class="invalid-feedback">
                Por favor, seleccione la ciudad de procedencia
              </div>
            </div>

            <div class="input-group mb-3">
              <span class="input-group-text">Teléfono:</span>
              <input id="input_phone_miembros" type="tel" class="form-control" aria-label="Campo para telefono el miembros" autocomplete="off" required>
              </select>
              <div class="invalid-feedback">
                Por favor, ingrese un número telefónico
              </div>
            </div>

            <div class="input-group mb-3">
              <span class="input-group-text">Fecha de inscripción:</span>
              <input id="input_fechainscrip_miembros" type="date" class="form-control" aria-label="Campo para la fecha de inscripción" autocomplete="off">
            </div>

            <div class="input-group mb-3">
              <span class="input-group-text">Club:</span>
              <input id="input_club_miembros" type="text" class="form-control" aria-label="Campo para el club del miembro" autocomplete="off">
            </div>

            <div class="form-check form-switch" style="margin-bottom: 3%; display: flex; justify-content: flex-end;">
              <input class="form-check-input" type="checkbox" role="switch" id="switch_estado_miembro" 
                style="background-color: #28a745; border-color: #28a745; border-radius: 50px; position: relative;" 
                onchange="cambiarColorSwitch_miembro(this)" checked>
              <label class="form-check-label" for="switch_estado_miembro" id="switchLabel_miembro" style="position: relative; margin-left: 2%;">
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
  function cambiarColorSwitch_miembro(checkbox) 
  {
    const label = document.getElementById("switchLabel_miembro");

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