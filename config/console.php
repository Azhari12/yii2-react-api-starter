<?php
$config_apps = require __DIR__ . '/config_apps.php';
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$db_rbac = require __DIR__ . '/db_rbac.php';

$config = [
    'id' => 'starter-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'db' => $db_rbac,
        ],
    ],
    'params' => $params,
];

return $config;
