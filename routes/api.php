<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TorneoController;
<<<<<<< HEAD
=======
use App\Http\Controllers\PairingSimulationController;
>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b

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

<<<<<<< HEAD
Route::get('/torneos/{torneo}/emparejamientos/{ronda}', [TorneoController::class, 'generarEmparejamientos']); 
=======
Route::get('/torneos/{torneo}/emparejamientos/{ronda}', [TorneoController::class, 'generarEmparejamientos']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/torneos/{torneo}/simular-ronda', [PairingSimulationController::class, 'simularRonda'])
        ->name('torneos.simular-ronda');
}); 
>>>>>>> e3a9c6968744e5bafed350125d9065973360a91b
