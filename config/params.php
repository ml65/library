<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'sms' => [
        'apiKey' => 'emulator',
    ],
    'book' => [
        'maxCoverSize' => 5 * 1024 * 1024, // 5MB
        'allowedCoverTypes' => ['image/jpeg', 'image/png'],
    ],
];
