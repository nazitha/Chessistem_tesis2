<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EvaluacionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:2');
    }

    /**
     * Display a listing of the evaluations.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        Log::info('EvaluacionController@index - Inicio del método');
        
        $user = Auth::user();
        Log::info('EvaluacionController@index - Usuario autenticado:', [
            'id' => $user->id_email,
            'correo' => $user->correo
        ]);

      
        return view('evaluaciones.index');
    }
} 
