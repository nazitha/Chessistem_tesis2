<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetUrl;
    public $token;

    public function __construct($resetUrl, $token)
    {
        $this->resetUrl = $resetUrl;
        $this->token = $token;
    }

    public function build()
    {
        return $this->view('emails.password-recovery')
                    ->subject('Recuperación de Contraseña - Estrellas del Ajedrez')
                    ->with([
                        'resetUrl' => $this->resetUrl,
                        'token' => $this->token
                    ]);
    }
} 