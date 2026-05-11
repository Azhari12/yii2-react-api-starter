<?php
/**
 * Configuration file - copy to config_apps.php and adjust values
 */
return [
    'config' => [
        'db' => [
            'postgre' => [
                'simrs' => [
                    'db_pg' => 'pgsql:host=localhost;port=5432;dbname=starter_db',
                    'user_pg' => 'postgres',
                    'pass_pg' => 'your_password',
                ],
            ],
        ],
        'url_apps' => [
            'sso' => '',
            'base' => 'http://app.starter.aa:5173/',
            'api' => 'http://api.starter.aa/',
        ],
    ],
];
