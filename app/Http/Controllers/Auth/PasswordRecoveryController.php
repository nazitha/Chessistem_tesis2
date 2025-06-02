<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\User;
use App\Models\Usuario;
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
        Log::info('=== INICIO DE SHOW RESET FORM ===');
        Log::info('Token recibido:', ['token' => $token]);

        $reset = DB::table('password_resets')
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        Log::info('Resultado de la búsqueda del token:', [
            'token_encontrado' => $reset ? true : false,
            'token' => $token,
            'now' => now()
        ]);

        if (!$reset) {
            Log::error('Token no válido o expirado en showResetForm');
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Este enlace de recuperación es inválido o ha expirado.']);
        }

        Log::info('Token válido, mostrando formulario de reset');
        return view('auth.reset-password', ['token' => $token, 'email' => $reset->correo]);
    }

    public function resetPassword(Request $request)
    {
        try {
            Log::info('=== INICIO DE RESET PASSWORD ===');
            Log::info('Datos del formulario:', [
                'token' => $request->token,
                'email' => $request->email,
                'password' => '******',
                'password_confirmation' => '******'
            ]);

            // Validar los datos
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed'
            ]);

            if ($validator->fails()) {
                Log::error('Validación fallida:', ['errors' => $validator->errors()->all()]);
                return back()->withErrors($validator)->withInput();
            }

            Log::info('Validación exitosa');

            // Verificar el token
            $reset = DB::table('password_resets')
                ->where('token', $request->token)
                ->where('correo', $request->email)
                ->where('expires_at', '>', now())
                ->first();

            if (!$reset) {
                Log::error('Token no válido o expirado', [
                    'token' => $request->token,
                    'email' => $request->email,
                    'now' => now()
                ]);
                return back()->withErrors(['email' => 'El enlace de recuperación es inválido o ha expirado.']);
            }

            Log::info('Token verificado correctamente');

            // Verificar si el usuario existe
            $user = DB::table('usuarios')->where('correo', $request->email)->first();
            if (!$user) {
                Log::error('Usuario no encontrado en la tabla usuarios', ['email' => $request->email]);
                return back()->withErrors(['email' => 'Usuario no encontrado.']);
            }

            Log::info('Usuario encontrado en la tabla usuarios');

            // Encriptar la contraseña
            $hashedPassword = Hash::make($request->password);
            Log::info('Contraseña encriptada generada');

            // Actualizar la contraseña
            Log::info('Intentando actualizar contraseña en la tabla usuarios');
            $updateResult = DB::table('usuarios')
                ->where('correo', $request->email)
                ->update([
                    'contrasena' => $hashedPassword,
                    'updated_at' => now()
                ]);

            Log::info('Resultado de la actualización:', ['success' => $updateResult]);

            if (!$updateResult) {
                Log::error('Error al actualizar la contraseña en la tabla usuarios');
                return back()->withErrors(['email' => 'Error al actualizar la contraseña.']);
            }

            // Eliminar el token usado
            DB::table('password_resets')
                ->where('correo', $request->email)
                ->delete();

            Log::info('=== RESET PASSWORD COMPLETADO EXITOSAMENTE ===');
            return redirect()->route('login')->with('status', 'Tu contraseña ha sido actualizada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error en resetPassword:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['email' => 'Ocurrió un error al actualizar la contraseña.']);
        }
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