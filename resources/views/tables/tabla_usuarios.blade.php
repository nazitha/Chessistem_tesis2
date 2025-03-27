<div id='contenedor-usuarios' class="p-8 mt-6 lg:mt-0 rounded shadow bg-white">

    <table id="tabla-usuarios" class="stripe hover" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
        <thead>
            <tr>
                <th data-priority="1">Correo</th>
                <th data-priority="2">Rol</th>
                <th data-priority="3">Permisos</th>
                <th data-priority="4">Estado</th>
                <th data-priority="5">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @foreach($usuarios as $usuario)
        <tr>
            <td>{{ $usuario->correo }}</td>
            <td>{{ $usuario->rol->nombre }}</td>
            <td>
                <button class="btn btn-primary btn-sm">Editar</button>
            </td>
        </tr>
        @endforeach

        </tbody>
    </table>
    
<script>
    $(document).ready(function() {
        $('#tabla_usuarios').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: ['excel']
        });
    });
</script>

</div>