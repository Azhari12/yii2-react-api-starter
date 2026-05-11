<?php

namespace app\models;

use Yii;
use yii\web\User as WebUser;

class User extends WebUser
{
    public function switchIdentity($identity, $duration = 0)
    {
        parent::switchIdentity($identity, $duration);
    }
}
