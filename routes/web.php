<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AcademiaController;
use App\Http\Controllers\CiudadController;
use App\Http\Controllers\EmparejamientoController;
use App\Http\Controllers\FederacionController;
use App\Http\Controllers\FideController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\ImportarAcademiasController;
use App\Http\Controllers\ImportarFidesController;
use App\Http\Controllers\ImportarInscripcionesController;
use App\Http\Controllers\ImportarMiembrosController;
use App\Http\Controllers\ImportarTorneosController;
use App\Http\Controllers\MiembroController;
use App\Http\Controllers\PartidaController;
use App\Http\Controllers\Auth\PasswordRecoveryController;
use App\Http\Controllers\TorneoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EvaluacionController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\TorneoViewController;
use App\Http\Controllers\TorneoParticipanteController;
use App\Http\Controllers\TorneoRondaController;

// Grupo web para todas las rutas
Route::middleware('web')->group(function () {
    // Redirección Home
    Route::get('/', function () {
        return Auth::check() ? redirect('/home') : redirect('/login');
    });

    // Rutas de autenticación
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);
        
        // Rutas de recuperación de contraseña
        Route::get('/password/reset', [PasswordRecoveryController::class, 'showForm'])->name('password.request');
        Route::post('/password/email', [PasswordRecoveryController::class, 'recoverPassword'])->name('password.email');
        Route::get('/password/reset/{token}', [PasswordRecoveryController::class, 'showResetForm'])->name('password.reset');
        Route::put('/password/update', [PasswordRecoveryController::class, 'resetPassword'])->name('password.update');
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Rutas protegidas por autenticación
    Route::middleware('auth')->group(function () {
        Route::get('/home', [HomeController::class, 'index'])->name('home');

        // Perfil de usuario y contraseña
        Route::get('/perfil', [UserController::class, 'profile'])->name('profile');
        Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

        // Rutas de administración
        Route::resource('usuarios', UserController::class);
        Route::resource('academias', AcademiaController::class);
        
        // Rutas de torneos
        Route::resource('torneos', TorneoController::class);
        Route::post('torneos/{torneo}/participantes', [TorneoParticipanteController::class, 'store'])
            ->name('torneos.participantes.store');
        Route::delete('torneos/{torneo}/participantes/{participante}', [TorneoParticipanteController::class, 'destroy'])
            ->name('torneos.participantes.destroy');
        Route::post('torneos/{torneo}/rondas', [TorneoRondaController::class, 'store'])
            ->name('torneos.rondas.store');
        Route::post('torneos/partidas/{partida}/resultado', [TorneoRondaController::class, 'registrarResultado'])
            ->name('torneos.partidas.resultado');
        Route::get('mis-torneos', [TorneoController::class, 'misTorneos'])->name('torneos.estudiante');

        Route::post('/asignar-permisos', [UserController::class, 'asignarPermisos'])->name('asignar.permisos');

        // Rutas de evaluador
        Route::get('/evaluaciones', [EvaluacionController::class, 'index'])->name('evaluaciones.index');

        // Rutas para recuperar contraseña
        Route::get('/password-recovery', [PasswordRecoveryController::class, 'showForm'])->name('password.recovery');
        Route::post('/password-recovery', [PasswordRecoveryController::class, 'recoverPassword'])->name('password.recovery.send');

        // Rutas para ciudades
        Route::resource('ciudades', CiudadController::class);

        // Rutas para emparejamientos
        Route::resource('emparejamientos', EmparejamientoController::class);

        // Rutas para federaciones
        Route::resource('federaciones', FederacionController::class);

        // Rutas para FIDE
        Route::resource('fides', FideController::class);

        // Rutas para historial
        Route::get('/historial', [HistorialController::class, 'cargarDatos']);

        // Rutas para importar datos
        Route::post('/importar/academias', [ImportarAcademiasController::class, 'importar']);
        Route::post('/importar/fides', [ImportarFidesController::class, 'importar']);
        Route::post('/importar/inscripciones', [ImportarInscripcionesController::class, 'importar']);
        Route::post('/importar/miembros', [ImportarMiembrosController::class, 'importar']);
        Route::post('/importar-torneos', [ImportarTorneosController::class, 'importar'])->name('torneos.importar');

        // Rutas para miembros
        Route::resource('miembros', MiembroController::class);

        // Rutas para partidas
        Route::resource('partidas', PartidaController::class);

        // Rutas para inscripciones
        Route::resource('inscripciones', InscripcionController::class);

        // Rutas para permisos
        Route::post('/asignar-permiso', [UserController::class, 'assignPermission']);
        Route::delete('/remover-permiso', [UserController::class, 'removePermission']);
    });

    // Rutas de autenticación de Google para recuperación de contraseña
    Route::get('/auth/google', [PasswordRecoveryController::class, 'redirectToGoogle'])->name('google.auth');
    Route::get('/auth/google/callback', [PasswordRecoveryController::class, 'handleGoogleCallback'])->name('google.callback');
});

/*Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/evaluador/dashboard', [EvaluadorController::class, 'dashboard'])->name('evaluador.dashboard');
    Route::get('/estudiante/dashboard', [EstudianteController::class, 'dashboard'])->name('estudiante.dashboard');
    Route::get('/gestor/dashboard', [GestorController::class, 'dashboard'])->name('gestor.dashboard');
});*/