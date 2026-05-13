<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\Cors;

class AuthController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['cors'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => [Yii::$app->params['frontendOrigin']],
                'Access-Control-Request-Method' => ['GET', 'POST', 'OPTIONS'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Request-Headers' => ['Content-Type', 'X-Requested-With', 'Accept', 'Authorization'],
                'Access-Control-Max-Age' => 86400,
            ],
        ];
        return $behaviors;
    }

    public function beforeAction($action)
    {
        if (Yii::$app->request->method === 'OPTIONS') {
            Yii::$app->response->getHeaders()
                ->set('Access-Control-Allow-Origin', Yii::$app->params['frontendOrigin'])
                ->set('Access-Control-Allow-Credentials', 'true')
                ->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                ->set('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Accept, Authorization')
                ->set('Access-Control-Max-Age', '86400');
            Yii::$app->response->setStatusCode(200);
            Yii::$app->response->content = '';
            Yii::$app->end();
        }
        return parent::beforeAction($action);
    }

    public function actionOptions()
    {
        Yii::$app->response->setStatusCode(200);
        return '';
    }

    public function actionCheckLogin()
    {

        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity;
            $auth = Yii::$app->authManager;

            $roles = array_keys($auth->getRolesByUser($user->id));
            $permissions = array_keys($auth->getPermissionsByUser($user->id));

            return [
                'success' => true,
                'status' => 'authenticated',
                'data' => [
                    'id' => Yii::$app->user->id,
                    'username' => $user->username,
                    'nama' => $user->nama,
                    'roles' => $roles,
                    'permissions' => $permissions,
                ],
            ];
        }

        Yii::$app->response->setStatusCode(401);
        return [
            'success' => false,
            'status' => 'unauthorized',
            'message' => 'User not authenticated',
        ];
    }

    public function actionLogout()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
        }

        return ['success' => true, 'message' => 'Logged out'];
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new \app\models\sso\LoginForm();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['success' => true];
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
}
