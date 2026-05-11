<?php

namespace app\components;

use Yii;
use yii\base\Component;

class SessionMiddleware extends Component
{
    public function checkSession()
    {
        // Skip for OPTIONS preflight requests
        if (Yii::$app->request->method === 'OPTIONS') {
            return;
        }
        
        // Skip for auth routes
        $route = Yii::$app->request->resolve()[0] ?? '';
        if (strpos($route, 'auth/') === 0 || strpos($route, 'debug/') === 0) {
            return;
        }
    }
}
