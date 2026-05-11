<?php

namespace app\modules\rbac\components;

use Yii;

class Helper
{
    public static function getRoutesByUser($userId)
    {
        $auth = Yii::$app->authManager;
        $permissions = $auth->getPermissionsByUser($userId);
        $routes = [];
        foreach ($permissions as $name => $permission) {
            if (strncmp($name, '/', 1) === 0) {
                $routes[] = $name;
            }
        }
        return $routes;
    }

    public static function checkRoute($route, $params = [])
    {
        $user = Yii::$app->user;
        if ($user->can($route)) {
            return true;
        }
        return false;
    }
}
