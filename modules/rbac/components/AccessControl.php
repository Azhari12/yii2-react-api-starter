<?php

namespace app\modules\rbac\components;

use Yii;
use yii\base\ActionFilter;

class AccessControl extends ActionFilter
{
    public $allowActions = [];

    public function beforeAction($action)
    {
        $route = '/' . $action->uniqueId;
        $module = $action->controller->module->id;

        // Allow listed actions
        foreach ($this->allowActions as $pattern) {
            if ($pattern === '*' || fnmatch($pattern, ltrim($route, '/'))) {
                return true;
            }
        }

        // Allow if user is guest (will be caught by auth check)
        if (Yii::$app->user->isGuest) {
            return true;
        }

        // Check RBAC permission
        $user = Yii::$app->user;
        if ($user->can($route)) {
            return true;
        }

        // Check without leading slash
        if ($user->can(ltrim($route, '/'))) {
            return true;
        }

        return true; // Default allow - restrict via permissions
    }
}
