<?php
return [
    'jwt_secret' => '1a81d193bb1a4abb80d0224f477488ff41876444468ef5676f5975606f9e8cb304cc2908a070caf2a255e3f9f173b012270edfc672624926cc7f3aa84f46c8f33c5552e7650bcf46370fa0c4ff172d32313f89bf8de81e941d5e8d4c3633dd290c7448bf8a66a6dbbc630c77a5fd8bd2c34cb778cd39eb77774c367d0acfeabc0c284b5a83a1d4c7084f8c52f920146490875740bd71c9f4fdd355d5481c9e830b0bca1bfd581aba18e29ebd5c2e30054f94eb6d72821c1ac49d842d714cfc13cfbcd3fd2e68aeef2550056be1b2e564af5f4f283e7615f57beb8ad321d3698c314f936b667cb0f987e334d79a9077f22f488c41657c48b8ab69932d31dfe719',
    'jwt_expire' => 3600, // secondi
    'mfa_issuer' => 'CoreSuite',
    'password_reset_expire' => 900, // secondi
    'max_login_attempts' => 5,
    'lockout_minutes' => 15,
    'remember_me_expire' => 2592000, // 30 giorni
];
