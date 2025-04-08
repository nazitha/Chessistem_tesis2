<?php

namespace App\Mail;

use Illuminate\Mail\Mailer;
use Illuminate\Mail\Transport\Transport;
use App\Services\BrevoMailService;

class BrevoMailer extends Mailer
{
    protected $brevoService;

    public function __construct(BrevoMailService $brevoService)
    {
        $this->brevoService = $brevoService;
    }

    public function send($view, array $data = [], $callback = null)
    {
        try {
            // Renderizar la vista
            $html = view($view, $data)->render();

            // Obtener el destinatario y asunto del callback
            $message = new \stdClass();
            $callback($message);

            // Enviar usando el servicio de Brevo
            return $this->brevoService->sendEmail(
                $message->to[0][0], // El primer destinatario
                $message->subject,
                $html
            );
        } catch (\Exception $e) {
            \Log::error('Error en BrevoMailer', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
} 