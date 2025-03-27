<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\PasswordRecoveryMail;
use Illuminate\Support\Str;

class PasswordRecoveryController extends Controller
{
    public function recoverPassword(Request $request)
    {
        // Validación del correo
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'No se ha proporcionado un correo electrónico válido.'
            ], 422);
        }

        $email = $request->email;

        // Buscar usuario
        $user = User::where('correo', $email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "El correo $email no está registrado en el sistema. Verifique e intente nuevamente."
            ], 404);
        }

        // Verificar estado del usuario
        if (!$user->usuario_estado) {
            return response()->json([
                'success' => false,
                'message' => "El correo $email se encuentra deshabilitado."
            ], 403);
        }

        // Manejo de intentos con cache
        $attemptsKey = "password_recovery_attempts:$email";
        $attempts = Cache::get($attemptsKey, ['attempts' => 0, 'last_attempt' => null]);

        // Reiniciar intentos si han pasado 15 minutos
        if ($attempts['last_attempt'] && now()->diffInMinutes($attempts['last_attempt']) >= 15) {
            $attempts['attempts'] = 0;
        }

        // Verificar máximo de intentos
        if ($attempts['attempts'] >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Ha excedido el número de intentos permitidos. Vuelva a intentarlo después de 15 minutos.'
            ], 429);
        }

        // Generar nueva contraseña
        $newPassword = Str::random(16);
        $user->contrasena = Hash::make($newPassword);
        
        if ($user->save()) {
            // Actualizar intentos
            $attempts['attempts']++;
            $attempts['last_attempt'] = now();
            Cache::put($attemptsKey, $attempts, now()->addMinutes(15));

            // Enviar correo
            try {
                Mail::to($email)->send(new PasswordRecoveryMail($newPassword));
                
                return response()->json([
                    'success' => true,
                    'message' => "Se ha enviado un correo con la nueva contraseña a $email."
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => "Hubo un error al enviar el correo a $email. Intenta nuevamente."
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Hubo un error al actualizar la contraseña. Intenta nuevamente.'
        ], 500);
    }
}