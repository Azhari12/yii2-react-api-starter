<?php

namespace app\modules\rbac\components;

use Yii;

class Configs
{
    private static $_instance;
    public $advanced = false;

    public static function instance()
    {
        if (self::$_instance === null) {
            self::$_instance = new static();
        }
        return self::$_instance;
    }

    public static function authManager()
    {
        return Yii::$app->authManager;
    }

    public static function db()
    {
        return Yii::$app->authManager->db ?? Yii::$app->db;
    }
}
