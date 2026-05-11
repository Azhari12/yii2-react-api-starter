<?php
return [
    'bsVersion' => '4.x',
    // SSO Configuration
    'config_sso' => true,
    // Frontend Origin for CORS
    'frontendOrigin' => YII_ENV_DEV ? 'http://app.starter.aa:5173' : 'http://app.starter.aa',
    // App Info
    'app' => [
        'name' => 'Starter Kit',
        'shortName' => 'STARTER',
        'version' => 'V.1.0',
    ],
];
