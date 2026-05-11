<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actionIndex()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return [
            'name' => Yii::$app->params['app']['name'],
            'version' => Yii::$app->params['app']['version'],
            'status' => 'running',
        ];
    }

    public function actionError()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return [
                'success' => false,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ];
        }
        return ['success' => false, 'message' => 'Unknown error'];
    }
}
