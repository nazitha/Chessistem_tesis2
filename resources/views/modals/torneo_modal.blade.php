<!-- Modal de Torneo -->
<div class="modal fade" id="torneoModal" tabindex="-1" role="dialog" aria-labelledby="torneoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="torneoModalLabel">Gestión de Torneo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTorneo">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nombre_torneo">Nombre del Torneo</label>
                            <input type="text" class="form-control" id="nombre_torneo" name="nombre_torneo" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tipo_torneo">Tipo de Torneo</label>
                            <select class="form-control" id="tipo_torneo" name="tipo_torneo" required>
                                <option value="">Seleccione un tipo...</option>
                                <option value="individual">Individual</option>
                                <option value="equipos">Por Equipos</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="fecha_inicio">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="fecha_fin">Fecha de Finalización</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="lugar">Lugar</label>
                            <input type="text" class="form-control" id="lugar" name="lugar" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="arbitro">Árbitro Principal</label>
                            <input type="text" class="form-control" id="arbitro" name="arbitro" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="guardarTorneo()">Guardar</button>
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