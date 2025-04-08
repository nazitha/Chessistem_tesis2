<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña - Estrellas del Ajedrez</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: #1a237e;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            font-size: 24px;
            margin: 0 auto;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 15px;
        }
        .content {
            padding: 40px 30px;
            background: white;
        }
        .button {
            display: inline-block;
            padding: 15px 35px;
            background-color: #1a237e;
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            margin: 25px 0;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .button:hover {
            background-color: #283593;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #666;
            border-top: 1px solid #eee;
        }
        .security-notice {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            font-size: 14px;
            color: #666;
            border-left: 4px solid #1a237e;
        }
        .chess-icon {
            font-size: 24px;
            margin-right: 10px;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 10px;
                width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><span style="font-size: 24px; display: inline-block; vertical-align: middle;">♔</span> Estrellas del Ajedrez</h1>
            <p>Academia de Ajedrez</p>
        </div>
        
        <div class="content">
            <h2>¡Hola!</h2>
            
            <p>Has solicitado restablecer tu contraseña en Estrellas del Ajedrez. Para continuar con el proceso, haz clic en el siguiente botón:</p>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="button">
                    Restablecer Contraseña
                </a>
            </div>
            
            <div class="security-notice">
                <strong>⚠️ Aviso de Seguridad:</strong>
                <p>Este enlace expirará en 1 hora por motivos de seguridad. Si no solicitaste restablecer tu contraseña, puedes ignorar este correo.</p>
            </div>

            <p>Si el botón no funciona, puedes copiar y pegar el siguiente enlace en tu navegador:</p>
            <p style="word-break: break-all; font-size: 12px; color: #666;">
                {{ $resetUrl }}
            </p>
        </div>
        
        <div class="footer">
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p>♔ Estrellas del Ajedrez © {{ date('Y') }}. Todos los derechos reservados.</p>
            <p>Formando futuros maestros del ajedrez</p>
        </div>
    </div>
</body>
</html> 