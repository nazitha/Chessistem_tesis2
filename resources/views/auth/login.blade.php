<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChestSystem Login</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="icon" href="{{ asset('img/estrellas_del_ajedrez_logo.jpeg') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container" id="container">
        <!-- Formulario de inicio de sesión -->
        <div class="login-form">
            <form method="POST" action="/login" id="loginForm">
                @csrf
                <h1 id="estrellas">Estrellas del Ajedrez</h1>
                <span>Ingrese sus credenciales</span>
                
                <input id="input_correo_login" 
                       type="email" 
                       placeholder="Ingrese su correo electrónico" 
                       name="correo" 
                       required 
                       value="{{ old('correo') }}"
                       class="@error('correo') error-input @enderror">
                
                @error('correo')
                    <div class="field-error">
                        {{ $message }}
                    </div>
                @enderror

                <div class="password">
                    <input type="password" 
                           placeholder="Ingrese su contraseña" 
                           name="contrasena" 
                           id="password" 
                           required
                           class="@error('contrasena') error-input @enderror">
                    <img src="{{ asset('img/icons8-hide-password-50.png') }}" 
                         class="pass-icon" 
                         onclick="togglePassword()" 
                         alt="Toggle password visibility">
                    @error('contrasena')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit">Iniciar Sesión</button>
                <!-- En móvil mostramos el acceso a recuperar contraseña al ocultar el panel derecho -->
                <a href="{{ route('password.request') }}" class="mobile-recover" style="display:none;">RECUPERAR CONTRASEÑA</a>
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

        // Esperar a que el DOM esté completamente cargado
        document.addEventListener('DOMContentLoaded', function() {
            // Limpiar mensajes de error cuando el usuario empiece a escribir
            const correoInput = document.getElementById('input_correo_login');
            const passwordInput = document.getElementById('password');

            if (correoInput) {
                correoInput.addEventListener('input', function() {
                    // Limpiar solo el error del campo de correo
                    const correoError = this.nextElementSibling;
                    if (correoError && correoError.classList.contains('field-error')) {
                        correoError.style.display = 'none';
                    }
                });
            }

            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    // Limpiar solo el error del campo de contraseña
                    const contrasenaError = this.parentElement.nextElementSibling;
                    if (contrasenaError && contrasenaError.classList.contains('field-error')) {
                        contrasenaError.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>