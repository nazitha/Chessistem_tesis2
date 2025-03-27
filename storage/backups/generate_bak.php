<?php
    // Establecer la zona horaria de Nicaragua (UTC-6)
    date_default_timezone_set('America/Managua');

    $backupDir = __DIR__ . '/backups/';

    // Generar el nombre del archivo de backup con la fecha y hora en el formato solicitado
    $backupFileName = 'backup_' . date('dmy_Hi') . '.bak';  // Ejemplo: backup_23022025_0810.bak

    include_once '../conections/object_conection.php';
    $objeto = new Conexion();
    $conexion = $objeto->Conectar();

    $dbName = 'nombre_de_tu_base_de_datos';

    function crearBackup($conexion, $dbName, $backupFileName, $backupDir) {
        $dumpCommand = "mysqldump --user=usuario --password=contraseña --host=localhost $dbName > $backupDir$backupFileName";
        exec($dumpCommand);
    }

    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0777, true);
    }

    crearBackup($conexion, $dbName, $backupFileName, $backupDir);

    // Eliminar backups antiguos si hay más de 5
    $backupFiles = glob($backupDir . '*.bak');

    usort($backupFiles, function($a, $b) {
        return filemtime($a) - filemtime($b);
    });

    // Mantener solo los 5 más recientes
    if (count($backupFiles) > 5) {
        unlink($backupFiles[0]);
    }

    echo "Backup creado exitosamente.";
?>
