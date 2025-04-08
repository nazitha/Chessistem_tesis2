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
        <!-- Formulario de inicio de sesión -->
        <div class="login-form">
            <form method="POST" action="{{ route('login.submit') }}" id="loginForm">
                @csrf
                <h1 id="estrellas">Estrellas del Ajedrez</h1>
                <span>Ingrese sus credenciales</span>
                
                <input id="input_correo_login" 
                       type="email" 
                       placeholder="Ingrese su correo electrónico" 
                       name="correo" 
                       required 
                       value="{{ old('correo') }}">
                <div class="password">
                    <input type="password" 
                           placeholder="Ingrese su contraseña" 
                           name="contrasena" 
                           id="password" 
                           required>
                    <img src="{{ asset('img/icons8-hide-password-50.png') }}" 
                         class="pass-icon" 
                         onclick="togglePassword()" 
                         alt="Toggle password visibility">
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <button type="submit">Iniciar Sesión</button>
            </form>
        </div>

        <!-- Complemento visual -->
        <div class="complement-container"> 
            <div class="complement">
                <div class="complement-panel complement-right">
                    <h1>Bienvenido</h1>
                    <p>¿Olvidaste tu contraseña?</p>
                    <a href="{{ route('password.request') }}">RECUPERAR CONTRASEÑA</a>
                </div>

                <div class="complement-panel complement-left">
                    <h1>Bienvenido</h1>
                    <p>¿Quieres volver al inicio de sesión?</p>
                    <button class="hidden" id="login">Iniciar Sesión</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.pass-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.style.opacity = '0.6';
            } else {
                passwordInput.type = 'password';
                toggleIcon.style.opacity = '0.9';
            }
        }

        // Limpiar mensaje de error cuando el usuario empiece a escribir
        document.getElementById('input_correo_login').addEventListener('input', function() {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.style.display = 'none';
            }
        });

        document.getElementById('password').addEventListener('input', function() {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.style.display = 'none';
            }
        });
    </script>
</body>
</html>