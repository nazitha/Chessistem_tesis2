<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña</title>
    <style>
        body {font-family: 'Montserrat', sans-serif; background-color: #f8f8f9; padding: 20px;}
        h1 {color: #1aa19c; text-align: center;}
        .content {background-color: #fff; padding: 20px; border-radius: 5px; text-align: center;}
        .new-pass {font-size: 24px; font-weight: bold; color: #2b303a;}
        p {color: #808389;}
    </style>
</head>
<body>
    <h1>Recuperación de Contraseña</h1>
    <div class="content">
        <p>Hemos detectado la solicitud de un cambio de contraseña. Utilice el siguiente pin para iniciar sesión:</p>
        <p class="new-pass">{{ $newPassword }}</p>
        <p>Recuerde cambiar la contraseña después de su primer inicio de sesión.</p>
    </div>
</body>
</html>