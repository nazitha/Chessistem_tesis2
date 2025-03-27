$(document).ready(function() {

    function iniciarBackup() {
        console.log("Iniciando el backup...");
        $.ajax({
            url: 'backups/generate_bak.php',
            type: 'GET',
            success: function(response) {
                //console.log("Backup generado exitosamente.");
                localStorage.setItem('lastBackup', new Date().toISOString());
                //console.log("Fecha y hora del último backup guardada: " + new Date().toISOString());
            },
            error: function(xhr, status, error) {
                //console.error("Hubo un error al generar el backup: " + error);
            }
        });
    }

    function checkAndRunBackup() {

        const lastBackup = localStorage.getItem('lastBackup');
        const now = new Date();
        //console.log("Fecha y hora actuales: " + now.toISOString());

        if (!lastBackup) {
            //console.log("Es la primera vez que se ejecuta el backup.");
            iniciarBackup();
            return;
        }

        const lastBackupDate = new Date(lastBackup);
        //console.log("Último backup realizado en: " + lastBackupDate.toISOString());

        const diffInMillis = now - lastBackupDate;
        //console.log("Diferencia en milisegundos desde el último backup: " + diffInMillis);

        if (diffInMillis >= 604800000) {
            //console.log("Han pasado 7 días o más desde el último backup. Iniciando nuevo backup...");
            iniciarBackup();
        } else {
            //console.log("Aún no han pasado 7 días desde el último backup. No es necesario ejecutar uno nuevo.");
        }
    }

    // Comprobar si ya pasó una semana y ejecutar el backup si es necesario
    checkAndRunBackup();
    setInterval(checkAndRunBackup, 86400000);
});
