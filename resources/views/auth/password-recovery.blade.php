<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Estrellas del Ajedrez</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="icon" href="{{ asset('img/estrellas_del_ajedrez_logo.jpeg') }}" type="image/x-icon">
</head>
<body>
    <div class="container">
        <div class="login-form">
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <h1 id="estrellas">Estrellas del Ajedrez</h1>
                <span>Recuperación de contraseña</span>

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

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
                       placeholder="Ingrese su correo electrónico" 
                       required 
                       value="{{ old('email') }}">

                <button type="submit">Enviar enlace de recuperación</button>
                <a href="{{ route('login') }}" class="back-to-login">Volver al inicio de sesión</a>
            </form>
        </div>

        <div class="complement-container">
            <div class="complement">
                <div class="complement-panel complement-right">
                    <h1>Recuperar Contraseña</h1>
                    <p>Ingresa tu correo electrónico y te enviaremos las instrucciones para restablecer tu contraseña.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 