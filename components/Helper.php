<?php

namespace app\components;

use Yii;

class Helper
{
    public static function createResponse($success, $data = null, $message = '')
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ];
    }
}
