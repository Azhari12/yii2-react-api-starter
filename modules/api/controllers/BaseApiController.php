<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\Cors;

class BaseApiController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'corsFilter' => [
                    'class' => Cors::class,
                    'cors' => [
                        'Origin' => [Yii::$app->params['frontendOrigin']],
                        'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                        'Access-Control-Request-Headers' => ['Content-Type', 'X-Requested-With', 'Accept', 'Authorization'],
                        'Access-Control-Allow-Credentials' => true,
                        'Access-Control-Allow-Headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
                    ],
                ],
            ]
        );
    }

    public function beforeAction($action)
    {
        if (Yii::$app->request->method === 'OPTIONS') {
            Yii::$app->response->getHeaders()
                ->set('Access-Control-Allow-Origin', Yii::$app->params['frontendOrigin'])
                ->set('Access-Control-Allow-Credentials', 'true')
                ->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->set('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Accept, Authorization')
                ->set('Access-Control-Max-Age', '86400');
            Yii::$app->response->setStatusCode(200);
            Yii::$app->response->content = '';
            Yii::$app->end();
        }
        return parent::beforeAction($action);
    }

    protected function success($data = null, $message = 'Berhasil')
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];
    }

    protected function error($message = 'Terjadi kesalahan', $statusCode = 400, $data = null)
    {
        Yii::$app->response->statusCode = $statusCode;
        return [
            'success' => false,
            'message' => $message,
            'data' => $data,
        ];
    }

    protected function paginated($dataProvider)
    {
        $pagination = $dataProvider->getPagination();
        return [
            'success' => true,
            'page' => $pagination->getPage() + 1,
            'pageSize' => $pagination->getPageSize(),
            'totalCount' => $dataProvider->getTotalCount(),
            'totalPages' => $pagination->getPageCount(),
            'data' => $dataProvider->getModels(),
        ];
    }

    public function actionOptions()
    {
        Yii::$app->response->setStatusCode(200);
        return '';
    }
}
