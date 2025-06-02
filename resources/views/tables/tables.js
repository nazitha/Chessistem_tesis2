$(document).ready(function () {
    $('#torneosTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('get.torneos') }}",
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'fecha' },
            { 
                data: null, 
                render: function (data, type, row) {
                    return `<button class="btn btn-primary">Editar</button>`;
                }
            }
        ]
    });
});
