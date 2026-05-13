<?php
return [
    'bsVersion' => '4.x',
    // SSO Configuration: set ke true untuk menggunakan SSO eksternal
    'config_sso' => true,
    // Frontend Origin untuk CORS — sesuaikan dengan URL frontend Anda
    'frontendOrigin' => YII_ENV_DEV ? 'http://app.yourdomain.com:5173' : 'http://app.yourdomain.com',
    // Informasi Aplikasi
    'app' => [
        'name'      => 'Starter Kit',
        'shortName' => 'STARTER',
        'version'   => 'V.1.0',
    ],
];
