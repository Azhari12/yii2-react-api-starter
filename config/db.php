<?php
return [
    'class' => 'yii\db\Connection',
    'dsn' => $config_apps['config']['db']['postgre']['simrs']['db_pg'],
    'username' => $config_apps['config']['db']['postgre']['simrs']['user_pg'],
    'password' => $config_apps['config']['db']['postgre']['simrs']['pass_pg'],
    'charset' => 'utf8',
    'schemaMap' => [
        'pgsql' => [
            'class' => 'yii\db\pgsql\Schema',
            'defaultSchema' => 'public'
        ]
    ],
];
