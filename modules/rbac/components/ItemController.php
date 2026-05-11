<?php

namespace app\modules\rbac\components;

use app\modules\rbac\models\AuthItem;
use Yii;
use yii\web\Controller;

class ItemController extends Controller
{
    public $enableCsrfValidation = false;

    protected function findModel($id)
    {
        $auth = Configs::authManager();
        $item = $auth->getPermission($id);
        if ($item === null) {
            $item = $auth->getRole($id);
        }
        if ($item !== null) {
            return new AuthItem($item);
        }
        return null;
    }
}
