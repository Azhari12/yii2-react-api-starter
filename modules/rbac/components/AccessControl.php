<?php

namespace app\modules\rbac\components;

use yii\web\ForbiddenHttpException;
use yii\base\Module;
use Yii;
use yii\web\User;
use yii\di\Instance;

/**
 * Access Control Filter (ACF) is a simple authorization method that is best used by applications that only need some simple access control. 
 * As its name indicates, ACF is an action filter that can be attached to a controller or a module as a behavior. 
 * ACF will check a set of access rules to make sure the current user can access the requested action.
 *
 * To use AccessControl, declare it in the application config as behavior.
 * For example.
 *
 * ```
 * 'as access' => [
 *     'class' => 'app\modules\rbac\components\AccessControl',
 *     'allowActions' => ['site/login', 'site/error']
 * ]
 * ```
 *
 * @property User $user
 * 
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class AccessControl extends \yii\base\ActionFilter
{
    /**
     * @var User User for check access.
     */
    private $_user = 'user';
    /**
     * @var array List of action that not need to check access.
     */
    public $allowActions = [];

    /**
     * Get user
     * @return User
     */
    public function getUser()
    {
        if (!$this->_user instanceof User) {
            $this->_user = Instance::ensure($this->_user, User::className());
        }
        return $this->_user;
    }

    /**
     * Set user
     * @param User|string $user
     */
    public function setUser($user)
    {
        $this->_user = $user;
    }

    /**
     * @inheritdoc
     */
    // public function beforeAction($action)
    // {

    //     if (\Yii::$app->request->isOptions) {
    //         \Yii::$app->response->setStatusCode(200);
    //         \Yii::$app->end();
    //         return true; // Tidak perlu lanjut ke controller
    //     }

    //     $actionId = $action->getUniqueId();
    //     $user = $this->getUser();
    //     if (Helper::checkRoute('/' . $actionId, Yii::$app->getRequest()->get(), $user)) {
    //         return true;
    //     }
    //     $this->denyAccess($user);
    // }

    public function beforeAction($action)
    {
        $response = \Yii::$app->response;
        $request = \Yii::$app->request;

        // ===== FIX CORS PERTAMA: SET HEADER UNTUK SEMUA REQUEST =====
        $frontendOrigin = \Yii::$app->params['frontendOrigin'] ?? '*';

        $response->headers->set('Access-Control-Allow-Origin', $frontendOrigin);
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Max-Age', '86400');

        // ===== FIX CORS KEDUA: HANDLE OPTIONS REQUEST =====
        if ($request->isOptions) {
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
            $response->setStatusCode(200);
            \Yii::$app->end();
            return true;
        }

        // ===== LANJUTKAN DENGAN RBAC =====
        $actionId = $action->getUniqueId();
        $user = $this->getUser();

        if (Helper::checkRoute('/' . $actionId, \Yii::$app->getRequest()->get(), $user)) {
            return true;
        }

        $this->denyAccess($user);
    }

    /**
     * Denies the access of the user.
     * The default implementation will redirect the user to the login page if he is a guest;
     * if the user is already logged, a 403 HTTP exception will be thrown.
     * @param  User $user the current user
     * @throws ForbiddenHttpException if the user is already logged in.
     */
    protected function denyAccess($user)
    {
        if ($user->getIsGuest()) {
            // ✅ Kirim 401 JSON untuk API
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            \Yii::$app->response->setStatusCode(401);
            echo \yii\helpers\Json::encode([
                'success' => false,
                'message' => 'Session expired. Please re-login.'
            ]);
            \Yii::$app->end(); // 🔥 HENTIKAN EKSEKUSI
        } else {
            // ✅ Untuk user login tapi tidak berhak → tetap 403
            throw new \yii\web\ForbiddenHttpException(
                Yii::t('yii', 'You are not allowed to perform this action.')
            );
        }
    }
    // protected function denyAccess($user)
    // {
    //     if ($user->getIsGuest()) {
    //         $user->loginRequired();
    //     } else {
    //         throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
    //     }
    // }

    /**
     * @inheritdoc
     */
    protected function isActive($action)
    {
        $uniqueId = $action->getUniqueId();
        if ($uniqueId === Yii::$app->getErrorHandler()->errorAction) {
            return false;
        }

        $user = $this->getUser();
        if ($user->getIsGuest()) {
            $loginUrl = null;
            if (is_array($user->loginUrl) && isset($user->loginUrl[0])) {
                $loginUrl = $user->loginUrl[0];
            } else if (is_string($user->loginUrl)) {
                $loginUrl = $user->loginUrl;
            }
            if (!is_null($loginUrl) && trim($loginUrl, '/') === $uniqueId) {
                return false;
            }
        }

        if ($this->owner instanceof Module) {
            // convert action uniqueId into an ID relative to the module
            $mid = $this->owner->getUniqueId();
            $id = $uniqueId;
            if ($mid !== '' && strpos($id, $mid . '/') === 0) {
                $id = substr($id, strlen($mid) + 1);
            }
        } else {
            $id = $action->id;
        }

        foreach ($this->allowActions as $route) {
            if (substr($route, -1) === '*') {
                $route = rtrim($route, "*");
                if ($route === '' || strpos($id, $route) === 0) {
                    return false;
                }
            } else {
                if ($id === $route) {
                    return false;
                }
            }
        }

        if ($action->controller->hasMethod('allowAction') && in_array($action->id, $action->controller->allowAction())) {
            return false;
        }

        return true;
    }
}
