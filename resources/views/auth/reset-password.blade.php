<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Estrellas del Ajedrez</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="icon" href="{{ asset('img/estrellas_del_ajedrez_logo.jpeg') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="login-form">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="token" value="{{ $token }}">
                
                <h1 id="estrellas">Estrellas del Ajedrez</h1>
                <span>Restablecer contraseña</span>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <input type="email" 
                       name="email" 
                       value="{{ $email ?? old('email') }}"
                       placeholder="Correo electrónico" 
                       required 
                       readonly>

                <div class="password">
                    <input type="password" 
                           name="password" 
                           id="password" 
                           placeholder="Nueva contraseña" 
                           required>
                    <img src="{{ asset('img/icons8-hide-password-50.png') }}" 
                         class="pass-icon" 
                         onclick="togglePassword('password')" 
                         alt="Toggle password visibility">
                </div>

                <div class="password">
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation" 
                           placeholder="Confirmar contraseña" 
                           required>
                    <img src="{{ asset('img/icons8-hide-password-50.png') }}" 
                         class="pass-icon" 
                         onclick="togglePassword('password_confirmation')" 
                         alt="Toggle password visibility">
                </div>

                <button type="submit">Restablecer Contraseña</button>
                <a href="{{ route('login') }}" class="back-to-login">Volver al inicio de sesión</a>
            </form>
        </div>

        <div class="complement-container">
            <div class="complement">
                <div class="complement-panel complement-right">
                    <h1>Restablecer Contraseña</h1>
                    <p>Ingresa y confirma tu nueva contraseña para continuar.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = passwordInput.nextElementSibling;
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.style.opacity = '0.6';
            } else {
                passwordInput.type = 'password';
                toggleIcon.style.opacity = '0.9';
            }
        }
    </script>
</body>
</html> 