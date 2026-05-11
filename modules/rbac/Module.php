<?php

namespace app\modules\rbac;

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\rbac\controllers';
    public $defaultRoute = 'default';
    public $userClassName;
    public $advanced = false;

    public function init()
    {
        parent::init();
        if ($this->userClassName === null) {
            $this->userClassName = Yii::$app->getUser()->identityClass;
        }
    }
}
