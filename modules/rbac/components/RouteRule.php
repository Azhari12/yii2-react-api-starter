<?php

namespace app\modules\rbac\components;

use yii\rbac\Rule;

class RouteRule extends Rule
{
    public $name = 'route_rule';

    public function execute($user, $item, $params)
    {
        return true;
    }
}
