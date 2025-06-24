@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Perfil de Usuario') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-4 p-3 bg-light rounded border">
                        <h5 class="mb-3">Información del Usuario</h5>
                        <ul class="list-unstyled mb-0">
                            <li><b>Correo:</b> {{ $user->correo }}</li>
                            <li><b>Rol:</b> {{ $user->rol ? $user->rol->nombre : 'Sin rol' }}</li>
                            <li><b>Estado:</b> {{ $user->usuario_estado ? 'Activo' : 'Inactivo' }}</li>
                            @if($user->miembro)
                                <li><b>Nombre:</b> {{ $user->miembro->nombres }} {{ $user->miembro->apellidos }}</li>
                                <li><b>Cédula:</b> {{ $user->miembro->cedula }}</li>
                                <li><b>Sexo:</b> {{ $user->miembro->getFormattedSexoAttribute() }}</li>
                                <li><b>Fecha de nacimiento:</b> {{ $user->miembro->fecha_nacimiento ? $user->miembro->fecha_nacimiento->format('d/m/Y') : '-' }}</li>
                                <li><b>Teléfono:</b> {{ $user->miembro->telefono ?? '-' }}</li>
                                <li><b>Academia:</b> {{ $user->miembro->academia ? $user->miembro->academia->nombre_academia : '-' }}</li>
                            @endif
                        </ul>
                    </div>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Correo Electrónico') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" value="{{ $user->correo }}" disabled>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="current_password" class="col-md-4 col-form-label text-md-right">{{ __('Contraseña Actual') }}</label>

                            <div class="col-md-6">
                                <input id="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" required>

                                @error('current_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Nueva Contraseña') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="password_confirmation" class="col-md-4 col-form-label text-md-right">{{ __('Confirmar Nueva Contraseña') }}</label>

                            <div class="col-md-6">
                                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Actualizar Contraseña') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 