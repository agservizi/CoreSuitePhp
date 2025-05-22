<?php
return [
    'jwt_secret' => 'CAMBIA_QUESTO_SECRET',
    'jwt_expire' => 3600, // secondi
    'mfa_issuer' => 'CoreSuite',
    'password_reset_expire' => 900, // secondi
    'max_login_attempts' => 5,
    'lockout_minutes' => 15,
    'remember_me_expire' => 2592000, // 30 giorni
];
