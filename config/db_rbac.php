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
            'defaultSchema' => 'starter_app'
        ]
    ],
    'on afterOpen' => function ($event) {
        $event->sender->createCommand("SET search_path TO starter_app;")->execute();
    },
];
