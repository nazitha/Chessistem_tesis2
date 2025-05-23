<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TorneoController;
use App\Http\Controllers\PairingSimulationController;

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