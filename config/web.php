<?php
$config_apps = require __DIR__ . '/config_apps.php';
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$db_sso = require __DIR__ . '/db_sso.php';
$db_rbac = require __DIR__ . '/db_rbac.php';
$params['config_apps'] = $config_apps;

$user = [];
if ($params['config_sso'] === true) {
    $user = [
        'class' => 'app\models\User',
        'identityClass' => 'app\models\Identitas',
        'enableAutoLogin' => true,
        'loginUrl' => $config_apps['config']['url_apps']['sso'] . 'masuk?b=' . $config_apps['config']['url_apps']['base'],
        'identityCookie' => ['name' => '_identity-id', 'path' => '/', 'httpOnly' => true, 'domain' => '.starter.aa'],
    ];
} else {
    $user = [
        'identityClass' => 'app\models\sso\User',
        'loginUrl' => ['auth/login'],
        'enableAutoLogin' => true,
    ];
}

$config = [
    'id' => 'starter-api',
    'basePath' => dirname(__DIR__),
    'language' => 'id-ID',
    'timeZone' => 'Asia/Jakarta',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'sso',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'session' => [
            'class' => 'yii\web\Session',
            'cookieParams' => [
                'lifetime' => 24 * 60 * 60,
                'httpOnly' => true,
                'path' => '/',
                'domain' => '.local.aa',
            ],
        ],
        'sessionMiddleware' => [
            'class' => 'app\components\SessionMiddleware',
        ],
        'user' => $user,
        'response' => [
            'charset' => 'UTF-8',
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG,
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => null,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'db_sso' => $db_sso,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // Auth
                // 'GET auth/check-login' => 'auth/check-login',
                // 'POST auth/logout' => 'auth/logout',
                // 'OPTIONS auth/<action>' => 'auth/options',

                // API Module - RBAC
                'GET api/rbac/routes' => 'api/rbac/routes',
                'POST api/rbac/assign-routes' => 'api/rbac/assign-routes',
                'POST api/rbac/remove-routes' => 'api/rbac/remove-routes',
                'GET api/rbac/permissions' => 'api/rbac/permissions',
                'POST api/rbac/create-permission' => 'api/rbac/create-permission',
                'PUT api/rbac/update-permission/<id:[^\/]+>' => 'api/rbac/update-permission',
                'DELETE api/rbac/delete-permission/<id:[^\/]+>' => 'api/rbac/delete-permission',
                'GET api/rbac/get-permission-by-id/<id:[^\/]+>' => 'api/rbac/get-permission-by-id',
                'POST api/rbac/assign-permission/<id:[^\/]+>' => 'api/rbac/assign-permission',
                'POST api/rbac/remove-permission/<id:[^\/]+>' => 'api/rbac/remove-permission',
                'GET api/rbac/roles' => 'api/rbac/roles',
                'POST api/rbac/create-role' => 'api/rbac/create-role',
                'PUT api/rbac/update-role/<id:[^\/]+>' => 'api/rbac/update-role',
                'DELETE api/rbac/delete-role/<id:[^\/]+>' => 'api/rbac/delete-role',
                'GET api/rbac/get-role-by-id/<id:[^\/]+>' => 'api/rbac/get-role-by-id',
                'POST api/rbac/assign-role/<id:[^\/]+>' => 'api/rbac/assign-role',
                'POST api/rbac/remove-role/<id:[^\/]+>' => 'api/rbac/remove-role',
                'GET api/rbac/get-user-assignment' => 'api/rbac/get-user-assignment',
                'GET api/rbac/get-item-assignment/<id:\d+>' => 'api/rbac/get-item-assignment',
                'POST api/rbac/assign-user/<id:\d+>' => 'api/rbac/assign-user',
                'POST api/rbac/remove-user-assign/<id:\d+>' => 'api/rbac/remove-user-assign',

                // API Module - Dashboard
                'GET api/dashboard/summary' => 'api/dashboard/summary',
                'GET api/dashboard/stats' => 'api/dashboard/stats',

                // API Module - Category (Example CRUD)
                'GET api/category' => 'api/category/index',
                'GET api/category/<id:\d+>' => 'api/category/view',
                'POST api/category' => 'api/category/create',
                'PUT api/category/<id:\d+>' => 'api/category/update',
                'DELETE api/category/<id:\d+>' => 'api/category/delete',

                // OPTIONS preflight - dengan id
                'OPTIONS api/<module>/<action>/<id>' => 'api/<module>/options',
                // OPTIONS preflight - dengan action (misal: api/rbac/roles)
                'OPTIONS api/<controller>/<action>' => 'api/<controller>/options',
                // OPTIONS preflight - tanpa action (misal: OPTIONS /api/category)
                'OPTIONS api/<controller>' => 'api/<controller>/options',
                // OPTIONS preflight untuk controller di root (misal: OPTIONS /auth/check-login)
                'OPTIONS <controller>/<action>' => '<controller>/options',
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'db' => $db_rbac,
        ],
    ],
    'on beforeRequest' => function ($event) {
        Yii::$app->sessionMiddleware->checkSession();
    },
    'modules' => [
        'api' => [
            'class' => 'app\modules\api\Module',
        ],
        'rbac' => [
            'class' => 'app\modules\rbac\Module',
        ],
    ],
    'as access' => [
        'class' => 'app\modules\rbac\components\AccessControl',
        'allowActions' => [
            'debug/*',
            'gii/*',
            'auth/*',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*'],
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
    if (file_exists(__DIR__ . '/web-local.php')) {
        $config = yii\helpers\ArrayHelper::merge($config, require __DIR__ . '/web-local.php');
    }
}

return $config;
