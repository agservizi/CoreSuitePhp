<?php
return [
    'app_name' => 'CoreSuite',
    'app_url' => 'https://app.coresuite.it',
    'app_env' => 'production',
    'timezone' => 'Europe/Rome',
    
    // Configurazioni per il caricamento dei file
    'upload_max_size' => 5 * 1024 * 1024, // 5MB in bytes
    'allowed_extensions' => ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'],
    'upload_path' => __DIR__ . '/../uploads/',
    
    // Configurazioni per l'autenticazione
    'session_lifetime' => 7200, // 2 ore in secondi
    'mfa_enabled' => true,
    'password_min_length' => 8,
    
    // Providers disponibili
    'providers' => [
        'phone' => ['Fastweb', 'Windtre', 'Pianeta Fibra'],
        'energy' => ['Enel Energia', 'Fastweb Energia', 'A2A Energia']
    ],
    
    // Configurazioni per notifiche push
    // Nota: queste sono chiavi di esempio e devono essere generate per la produzione
    // usando uno strumento come web-push-codelab.glitch.me
    'push_public_key' => 'BNv0z_RrYxUwp8pLkFtew1p4iyzmQN6RyNqr1C9cBGPXx9IA_avPtuIEfzXgmYAGx7gU8ir8yHSXsI5hAEx6N3s',
    'push_private_key' => 'GvkbXzk9yX9t4YKsKn9Qw8WQN4UMiXRK-ixrKmrIGKM',
    
    // Configurazioni per l'invio di email
    'email' => [
        'host' => 'smtp.gmail.com',     // Modificare con il proprio server SMTP
        'port' => 587,                   // Porta SMTP (in genere 587 per TLS, 465 per SSL)
        'username' => 'info@coresuite.it', // Email mittente
        'password' => 'password_sicura',   // Password dell'email mittente
        'from' => 'info@coresuite.it',     // Email da cui vengono inviate le email
        'from_name' => 'CoreSuite System' // Nome visualizzato come mittente
    ]
];
?>
