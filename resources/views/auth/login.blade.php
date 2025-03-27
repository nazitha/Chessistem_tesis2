<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChestSystem Login</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="icon" href="img/estrellas_del_ajedrez_logo.jpeg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container" id="container">
        <!-- Mensajes de éxito o error -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario de inicio de sesión -->
        <div class="login-form">
            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <h1 id="estrellas">Estrellas del Ajedrez</h1>
                <span>Ingrese sus credenciales</span>
                <input id="input_correo_login" type="email" placeholder="Ingrese su correo electrónico" name="correo" required>
                <div class="password">
                    <input type="password" placeholder="Ingrese su contraseña" name="password" id="password" required>
                </div>
                <button type="submit">Iniciar Sesión</button>
            </form>
        </div>

        <!-- Complemento visual -->
        <div class="complement-container"> 
            <div class="complement">
                <div class="complement-panel complement-right">
                    <h1>Bienvenido</h1>
                    <p>¿Olvidaste tu contraseña?</p>
                    <button class="hidden" id="register">Recuperar contraseña</button>
                </div>

                <div class="complement-panel complement-left">
                    <h1>Bienvenido</h1>
                    <p>¿Quieres volver al inicio de sesión?</p>
                    <button class="hidden" id="login">Iniciar Sesión</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>