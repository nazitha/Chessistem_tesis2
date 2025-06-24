$(document).ready(function() {
    $('#contenedor-usuarios').hide();
    $('#contenedor-federaciones').hide();
    $('#contenedor-historial').hide();
    $('#contenedor-ciudades').hide();
    $('#contenedor-asigpermisos').hide();
    $('#contenedor-academias').hide();
    $('#contenedor-miembros').hide();
    $('#contenedor-fide').hide();
    $('#contenedor-torneos').hide();
    $('#contenedor-inscripciones').hide();
    $('#contenedor-partidas').hide();

    $('#usuarios_opcion, #usuarios_opcion_movil').click(function(e) {
        if (!$(this).attr('href')) {
            e.preventDefault();
            $('#encabezado_body').text('Usuarios');
            $('#contenedor-usuarios').show();
            
            $('#contenedor-asigpermisos').hide();
            $('#contenedor-federaciones').hide();
            $('#contenedor-historial').hide();
            $('#contenedor-ciudades').hide();
            $('#contenedor-academias').hide();
            $('#contenedor-miembros').hide();
            $('#contenedor-fide').hide();
            $('#contenedor-torneos').hide();
            $('#contenedor-inscripciones').hide();
            $('#contenedor-partidas').hide();
        }
    });

    $('#asigpermis_opcion, #asigpermis_opcion_movil').click(function(e) {
        if (!$(this).attr('href')) {
            e.preventDefault();
            $('#encabezado_body').text('Asignación de permisos');
            $('#contenedor-asigpermisos').show();

            $('#contenedor-usuarios').hide();
            $('#contenedor-federaciones').hide();
            $('#contenedor-historial').hide();
            $('#contenedor-ciudades').hide();
            $('#contenedor-academias').hide();
            $('#contenedor-miembros').hide();
            $('#contenedor-fide').hide();
            $('#contenedor-torneos').hide();
            $('#contenedor-inscripciones').hide();
            $('#contenedor-partidas').hide();
        }
    });

    $('#home_opcion_movil, #home_opcion').click(function(e) {
        if (!$(this).attr('href')) {
            e.preventDefault();
            $('#encabezado_body').text('Home');

            $('#contenedor-usuarios').hide();
            $('#contenedor-asigpermisos').hide();
            $('#contenedor-federaciones').hide();
            $('#contenedor-historial').hide();
            $('#contenedor-ciudades').hide();
            $('#contenedor-academias').hide();
            $('#contenedor-miembros').hide();
            $('#contenedor-fide').hide();
            $('#contenedor-torneos').hide();
            $('#contenedor-inscripciones').hide();
            $('#contenedor-partidas').hide();
        }
    });

    $('#miembros_opcion, #miembros_opcion_movil').click(function(e) {
        if (!$(this).attr('href')) {
            e.preventDefault();
            $('#encabezado_body').text('Miembros');
            $('#contenedor-miembros').show();

            $('#contenedor-usuarios').hide();
            $('#contenedor-asigpermisos').hide();
            $('#contenedor-federaciones').hide();
            $('#contenedor-historial').hide();
            $('#contenedor-ciudades').hide();
            $('#contenedor-academias').hide();
            $('#contenedor-fide').hide();
            $('#contenedor-torneos').hide();
            $('#contenedor-inscripciones').hide();
            $('#contenedor-partidas').hide();
        }
    });

    $('#academias_opcion, #academias_opcion_movil').click(function(e) {
        if (!$(this).attr('href')) {
            e.preventDefault();
            $('#encabezado_body').text('Academias');
            $('#contenedor-academias').show();

            $('#contenedor-usuarios').hide();
            $('#contenedor-asigpermisos').hide();
            $('#contenedor-federaciones').hide();
            $('#contenedor-historial').hide();
            $('#contenedor-ciudades').hide();
            $('#contenedor-miembros').hide();
            $('#contenedor-fide').hide();
            $('#contenedor-torneos').hide();
            $('#contenedor-inscripciones').hide();
            $('#contenedor-partidas').hide();
        }
    });

    $('#federaciones_opcion_movil, #federaciones_opcion').click(function(e) {
        if (!$(this).attr('href')) {
            e.preventDefault();
            $('#encabezado_body').text('Federaciones');
            $('#contenedor-federaciones').show();

            $('#contenedor-asigpermisos').hide();
            $('#contenedor-usuarios').hide();
            $('#contenedor-historial').hide();
            $('#contenedor-ciudades').hide();
            $('#contenedor-academias').hide();
            $('#contenedor-miembros').hide();
            $('#contenedor-fide').hide();
            $('#contenedor-torneos').hide();
            $('#contenedor-inscripciones').hide();
            $('#contenedor-partidas').hide();
        }
    });

    $('#inscripciones_opcion, #inscripciones_opcion_movil').click(function(e) {
        if (!$(this).attr('href')) {
            e.preventDefault();
            $('#encabezado_body').text('Inscripciones');
            $('#contenedor-inscripciones').show();

            $('#contenedor-usuarios').hide();
            $('#contenedor-asigpermisos').hide();
            $('#contenedor-federaciones').hide();
            $('#contenedor-historial').hide();
            $('#contenedor-ciudades').hide();
            $('#contenedor-academias').hide();
            $('#contenedor-miembros').hide();
            $('#contenedor-fide').hide();
            $('#contenedor-torneos').hide();
            $('#contenedor-partidas').hide();
        }
    });

    $('#torneos_y_partidas_opcion, #torneos_y_partidas_opcion_movil').click(function(e) {
        if (!$(this).attr('href')) {
            e.preventDefault();
            $('#encabezado_body').text('Torneos y partidas');
            $('#contenedor-torneos').show();

            $('#contenedor-usuarios').hide();
            $('#contenedor-asigpermisos').hide();
            $('#contenedor-federaciones').hide();
            $('#contenedor-historial').hide();
            $('#contenedor-ciudades').hide();
            $('#contenedor-academias').hide();
            $('#contenedor-miembros').hide();
            $('#contenedor-fide').hide();
            $('#contenedor-inscripciones').hide();
            $('#contenedor-partidas').hide();
        }
    });

    $('#historial_opcion, #historial_opcion_movil').click(function(e) {
        if (!$(this).attr('href')) {
            e.preventDefault();
            $('#encabezado_body').text('Historial');
            $('#contenedor-historial').show();

            $('#contenedor-asigpermisos').hide();     
            $('#contenedor-federaciones').hide();
            $('#contenedor-ciudades').hide();
            $('#contenedor-usuarios').hide();
            $('#contenedor-academias').hide();
            $('#contenedor-miembros').hide();
            $('#contenedor-fide').hide();
            $('#contenedor-torneos').hide();
            $('#contenedor-inscripciones').hide();
            $('#contenedor-partidas').hide();
        }
    });

    $('#ciudades_opcion, #ciudades_opcion_movil').click(function(e) {
        if (!$(this).attr('href')) {
            e.preventDefault();
            $('#encabezado_body').text('Ciudades');
            $('#contenedor-ciudades').show();
            
            $('#contenedor-asigpermisos').hide();
            $('#contenedor-usuarios').hide();
            $('#contenedor-federaciones').hide();
            $('#contenedor-historial').hide();
            $('#contenedor-academias').hide();
            $('#contenedor-miembros').hide();
            $('#contenedor-fide').hide();
            $('#contenedor-torneos').hide();
            $('#contenedor-inscripciones').hide();
            $('#contenedor-partidas').hide();
        }
    });

    $('#fide_opcion, #fide_opcion_movil').click(function(e) {
        if (!$(this).attr('href')) {
            e.preventDefault();
            $('#encabezado_body').text('FIDE');
            $('#contenedor-fide').show();
            
            $('#contenedor-ciudades').hide();
            $('#contenedor-asigpermisos').hide();
            $('#contenedor-usuarios').hide();
            $('#contenedor-federaciones').hide();
            $('#contenedor-historial').hide();
            $('#contenedor-academias').hide();
            $('#contenedor-miembros').hide();
            $('#contenedor-torneos').hide();
            $('#contenedor-inscripciones').hide();
            $('#contenedor-partidas').hide();
        }
    });

    $('#partidas_opcion').click(function(e) {
        if (!$(this).attr('href')) {
            e.preventDefault();
            $('#encabezado_body').text('Partidas');
            $('#contenedor-partidas').show();
            
            $('#contenedor-fide').hide();
            $('#contenedor-ciudades').hide();
            $('#contenedor-asigpermisos').hide();
            $('#contenedor-usuarios').hide();
            $('#contenedor-federaciones').hide();
            $('#contenedor-historial').hide();
            $('#contenedor-academias').hide();
            $('#contenedor-miembros').hide();
            $('#contenedor-torneos').hide();
            $('#contenedor-inscripciones').hide();
        }
    });
});
