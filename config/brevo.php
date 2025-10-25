<?php

return [
    'smtp' => [
        'host' => env('MAIL_HOST', 'smtp-relay.brevo.com'),
        'port' => env('MAIL_PORT', 587),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    ],
]; 