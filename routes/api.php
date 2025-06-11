<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TorneoController;
use App\Http\Controllers\PairingSimulationController;
use App\Http\Controllers\PartidaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/torneos/{torneo}/emparejamientos/{ronda}', [TorneoController::class, 'generarEmparejamientos']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/torneos/{torneo}/simular-ronda', [PairingSimulationController::class, 'simularRonda'])
        ->name('torneos.simular-ronda');
});

// Partidas routes
Route::prefix('partidas')->group(function () {
    Route::get('torneo/{torneoId}', [PartidaController::class, 'index']);
    Route::get('torneo/{torneoId}/ronda/{ronda}', [PartidaController::class, 'getPartidasByRonda']);
    Route::post('/', [PartidaController::class, 'store']);
    Route::get('/{id}', [PartidaController::class, 'show']);
    Route::put('/{id}', [PartidaController::class, 'update']);
    Route::delete('/{id}', [PartidaController::class, 'destroy']);
    
    // Nuevas rutas para generar partidas
    Route::post('torneo/{torneo}/round-robin', [PartidaController::class, 'generarPartidasRoundRobin']);
    Route::post('torneo/{torneo}/eliminacion-directa', [PartidaController::class, 'generarPartidasEliminacionDirecta']);
    Route::post('torneo/{torneo}/suizo', [PartidaController::class, 'generarPartidasSuizo']);
}); 