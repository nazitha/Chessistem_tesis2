<?php

namespace App\Services;

use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class BrevoMailService
{
    private $apiInstance;

    public function __construct()
    {
        try {
            $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', config('mail.brevo.key'));
            Log::info('Configurando Brevo con API key', ['key' => substr(config('mail.brevo.key'), 0, 10) . '...']);
            
            $this->apiInstance = new TransactionalEmailsApi(
                new Client(),
                $config
            );
            
            Log::info('BrevoMailService inicializado correctamente');
        } catch (\Exception $e) {
            Log::error('Error al inicializar BrevoMailService', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function sendEmail($to, $subject, $htmlContent)
    {
        try {
            Log::info('Enviando correo a través de Brevo API');
            
            $sendSmtpEmail = new SendSmtpEmail([
                'to' => [['email' => $to]],
                'sender' => [
                    'name' => config('mail.from.name', 'Estrellas del Ajedrez'),
                    'email' => config('mail.from.address', 'noreply@estrellasdelajedrez.com')
                ],
                'subject' => $subject,
                'htmlContent' => $htmlContent
            ]);

            $result = $this->apiInstance->sendTransacEmail($sendSmtpEmail);
            Log::info('Correo enviado exitosamente', ['messageId' => $result->getMessageId()]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error al enviar correo a través de Brevo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
} 