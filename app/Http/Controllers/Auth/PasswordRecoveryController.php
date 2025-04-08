<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;

class PasswordRecoveryController extends Controller
{
    public function showForm()
    {
        return view('auth.password-recovery');
    }

    public function recoverPassword(Request $request)
    {
        try {
            Log::info('Iniciando proceso de recuperación de contraseña');
            
            $validator = Validator::make($request->all(), [
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {
                Log::error('Validación fallida', ['errors' => $validator->errors()]);
                return back()->withErrors(['email' => 'Por favor, ingrese un correo electrónico válido.'])->withInput();
            }

            $correo = $request->email;
            Log::info('Buscando usuario', ['correo' => $correo]);
            
            $user = User::where('correo', $correo)->first();

            if (!$user) {
                Log::warning('Usuario no encontrado', ['correo' => $correo]);
                return back()->withErrors(['email' => 'No encontramos una cuenta con ese correo electrónico.'])->withInput();
            }

            if (!$user->usuario_estado) {
                Log::warning('Usuario deshabilitado', ['correo' => $correo]);
                return back()->withErrors(['email' => 'Esta cuenta se encuentra deshabilitada.'])->withInput();
            }

            $token = Str::random(60);
            Log::info('Token generado');

            // Eliminar tokens anteriores para este correo
            DB::table('password_resets')->where('correo', $correo)->delete();

            // Guardar el nuevo token
            DB::table('password_resets')->insert([
                'correo' => $correo,
                'token' => $token,
                'created_at' => now(),
                'expires_at' => now()->addHour()
            ]);

            $resetUrl = url('/password/reset/' . $token);

            // Enviar el correo
            Mail::to($correo)->send(new PasswordResetMail($resetUrl, $token));

            Log::info('Correo enviado exitosamente');
            return back()->with('status', 'Te hemos enviado un correo con las instrucciones para recuperar tu contraseña.');
        } catch (\Exception $e) {
            Log::error('Error al enviar correo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['email' => 'Ocurrió un error al enviar el correo. Por favor, intenta de nuevo.'])->withInput();
        }
    }

    public function showResetForm($token)
    {
        $reset = DB::table('password_resets')
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$reset) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Este enlace de recuperación es inválido o ha expirado.']);
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $reset->correo]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $reset = DB::table('password_resets')
            ->where('token', $request->token)
            ->where('correo', $request->email)
            ->where('expires_at', '>', now())
            ->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'Este enlace de recuperación es inválido o ha expirado.']);
        }

        $user = User::where('correo', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No encontramos una cuenta con ese correo electrónico.']);
        }

        $user->contrasena = $request->password;
        $user->save();

        // Eliminar todos los tokens de recuperación para este usuario
        DB::table('password_resets')->where('correo', $request->email)->delete();

        return redirect()->route('login')
            ->with('status', 'Tu contraseña ha sido actualizada correctamente. Ya puedes iniciar sesión.');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }
}