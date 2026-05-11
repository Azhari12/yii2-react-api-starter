<?php

namespace app\components;

use Yii;

class Akun
{
    public static function user()
    {
        return Yii::$app->user->identity;
    }

    public static function userId()
    {
        return Yii::$app->user->id;
    }

    public static function isGuest()
    {
        return Yii::$app->user->isGuest;
    }
}
