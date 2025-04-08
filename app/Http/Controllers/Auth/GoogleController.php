<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordRecoveryMail;

class GoogleController_disabled extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            
            // Aquí podemos usar el token de acceso para enviar correos
            config([
                'mail.mailer' => 'smtp',
                'mail.host' => 'smtp.gmail.com',
                'mail.port' => 587,
                'mail.encryption' => 'tls',
                'mail.username' => $user->email,
                'mail.password' => $user->token,
            ]);

            return redirect()->route('login')
                ->with('status', 'Autenticación con Google completada exitosamente.');
        } catch (Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Error al autenticar con Google: ' . $e->getMessage()]);
        }
    }
} 