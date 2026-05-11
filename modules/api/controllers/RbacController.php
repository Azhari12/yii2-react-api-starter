<?php

namespace app\modules\api\controllers;

use app\modules\rbac\components\Configs;
use app\modules\rbac\models\Assignment;
use app\modules\rbac\models\AuthItem;
use app\modules\rbac\models\Route;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;

class RbacController extends BaseApiController
{
    // ==================== ROUTES ====================

    public function actionRoutes()
    {
        $model = new Route();
        $routes = $model->getRoutes();

        return $this->success([
            'available_routes' => array_values($routes['available']),
            'assigned_routes' => array_values($routes['assigned']),
        ]);
    }

    public function actionAssignRoutes()
    {
        $data = Yii::$app->request->post();
        $model = new Route();
        $model->addNew($data);
        return $this->success(null, 'Routes assigned successfully.');
    }

    public function actionRemoveRoutes()
    {
        $data = Yii::$app->request->post();
        $model = new Route();
        $model->remove($data);
        return $this->success(null, 'Routes removed successfully.');
    }

    // ==================== PERMISSIONS ====================

    public function actionPermissions()
    {
        $authManager = Configs::authManager();
        $advanced = Configs::instance()->advanced;

        $permissions = array_filter($authManager->getPermissions(), function ($item) use ($advanced) {
            if ($advanced) {
                return !(strncmp($item->name, '/', 1) === 0 || strncmp($item->name, '@', 1) === 0);
            }
            return strncmp($item->name, '/', 1) !== 0;
        });

        return $this->success(array_keys($permissions));
    }

    public function actionCreatePermission()
    {
        $data = Yii::$app->request->post();
        $model = new AuthItem(null);
        $model->type = Item::TYPE_PERMISSION;

        if ($model->load($data) && $model->save()) {
            return $this->success(null, 'Permission created successfully.');
        }

        return $this->error('Failed to create permission.', 400, $model->getErrors());
    }

    public function actionUpdatePermission($id)
    {
        $data = Yii::$app->request->post();
        $model = $this->findAuthItem($id);

        if (!$model) {
            return $this->error('Permission not found.', 404);
        }

        if ($model->load($data) && $model->save()) {
            return $this->success(null, 'Permission updated successfully.');
        }

        return $this->error('Failed to update permission.', 400, $model->getErrors());
    }

    public function actionDeletePermission($id)
    {
        $model = $this->findAuthItem($id);
        if (!$model) {
            return $this->error('Permission not found.', 404);
        }

        if (Configs::authManager()->remove($model->item)) {
            return $this->success(null, 'Permission removed successfully.');
        }

        return $this->error('Failed to remove permission.', 400);
    }

    public function actionGetPermissionById($id)
    {
        $model = $this->findAuthItem($id);
        if (!$model) {
            return $this->error('Permission not found.', 404);
        }

        return $this->success($model->getItems());
    }

    public function actionAssignPermission($id)
    {
        $data = Yii::$app->request->post();
        $model = $this->findAuthItem($id);

        if ($model && $model->addChildren($data)) {
            return $this->success(null, 'Berhasil assign pada permission ' . $id);
        }

        return $this->error('Failed to assign permission.', 400);
    }

    public function actionRemovePermission($id)
    {
        $data = Yii::$app->request->post();
        $model = $this->findAuthItem($id);

        if ($model && $model->removeChildren($data)) {
            return $this->success(null, 'Berhasil unassign pada permission ' . $id);
        }

        return $this->error('Failed to remove permission.', 400);
    }

    // ==================== ROLES ====================

    public function actionRoles()
    {
        $authManager = Configs::authManager();
        return $this->success(array_keys($authManager->getRoles()));
    }

    public function actionCreateRole()
    {
        $data = Yii::$app->request->post();
        $model = new AuthItem(null);
        $model->type = Item::TYPE_ROLE;

        if ($model->load($data) && $model->save()) {
            return $this->success(null, 'Role berhasil ditambah.');
        }

        return $this->error('Gagal menambah role.', 400, $model->getErrors());
    }

    public function actionUpdateRole($id)
    {
        $data = Yii::$app->request->post();
        $auth = Configs::authManager();
        $item = $auth->getRole($id);
        $model = new AuthItem($item);

        if ($model->load($data) && $model->save()) {
            return $this->success(null, 'Role berhasil diubah.');
        }

        return $this->error('Gagal ubah role.', 400, $model->getErrors());
    }

    public function actionDeleteRole($id)
    {
        $auth = Configs::authManager();
        $item = $auth->getRole($id);
        $model = new AuthItem($item);

        if (Configs::authManager()->remove($model->item)) {
            return $this->success(null, 'Berhasil menghapus role.');
        }

        return $this->error('Role not found.', 404);
    }

    public function actionGetRoleById($id)
    {
        $auth = Configs::authManager();
        $item = $auth->getRole($id);
        $model = new AuthItem($item);

        if (!$model) {
            return $this->error('Role not found.', 404);
        }

        return $this->success($model->getItems());
    }

    public function actionAssignRole($id)
    {
        $data = Yii::$app->request->post();
        $auth = Configs::authManager();
        $item = $auth->getRole($id);
        $model = new AuthItem($item);
        $model->type = Item::TYPE_ROLE;

        if ($model->addChildren($data)) {
            return $this->success(null, 'Berhasil assign pada Role ' . $id);
        }

        return $this->error('Failed to assign role.', 400);
    }

    public function actionRemoveRole($id)
    {
        $data = Yii::$app->request->post();
        $auth = Configs::authManager();
        $item = $auth->getRole($id);
        $model = new AuthItem($item);
        $model->type = Item::TYPE_ROLE;

        if ($model->removeChildren($data)) {
            return $this->success(null, 'Berhasil unassign pada Role ' . $id);
        }

        return $this->error('Failed to remove role.', 400);
    }

    // ==================== ASSIGNMENT ====================

    public function actionGetUserAssignment()
    {
        $class = Yii::$app->getUser()->identityClass ?: 'app\models\sso\User';
        $request = Yii::$app->request;
        $search = $request->get('name', '');

        $query = $class::find();
        if (!empty($search)) {
            $query->andFilterWhere(['ilike', 'nama', $search]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $request->get('page_size', 10),
                'page' => $request->get('page', 1) - 1,
            ],
        ]);

        return $this->paginated($dataProvider);
    }

    public function actionGetItemAssignment($id)
    {
        $class = 'app\models\sso\User';
        $user = $class::findIdentity($id);

        if ($user === null) {
            return $this->error('User not found.', 404);
        }

        $model = new Assignment($id, $user);
        return $this->success($model->getItems());
    }

    public function actionAssignUser($id)
    {
        $data = Yii::$app->request->post();
        $model = new Assignment($id);

        if ($model->assign($data)) {
            return $this->success(null, 'Berhasil assign pada User ' . $id);
        }

        return $this->error('Gagal assign pada User ' . $id, 400);
    }

    public function actionRemoveUserAssign($id)
    {
        $data = Yii::$app->request->post();
        $model = new Assignment($id);

        if ($model->revoke($data)) {
            return $this->success(null, 'Berhasil unassign pada User ' . $id);
        }

        return $this->error('Gagal unassign pada User ' . $id, 400);
    }

    // ==================== HELPERS ====================

    private function findAuthItem($id)
    {
        $auth = Configs::authManager();
        $item = $auth->getPermission($id);
        if ($item === null) {
            $item = $auth->getRole($id);
        }
        if ($item === null) {
            return null;
        }
        return new AuthItem($item);
    }
}
