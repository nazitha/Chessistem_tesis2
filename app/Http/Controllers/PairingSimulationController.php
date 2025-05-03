<?php

namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Services\PairingSimulationService;
use Illuminate\Http\Request;

class PairingSimulationController extends Controller
{
    public function simularRonda(Torneo $torneo)
    {
        $service = new PairingSimulationService($torneo);
        $resultado = $service->simularRonda();

        return response()->json([
            'emparejamientos' => $resultado['emparejamientos'],
            'advertencias' => $resultado['advertencias']
        ]);
    }
} 