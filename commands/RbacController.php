<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        // Create permissions
        $dashboard = $auth->createPermission('dashboard');
        $dashboard->description = 'Access dashboard';
        $auth->add($dashboard);

        $categories = $auth->createPermission('categories');
        $categories->description = 'Manage categories';
        $auth->add($categories);

        $rbac = $auth->createPermission('rbac');
        $rbac->description = 'Manage RBAC';
        $auth->add($rbac);

        $root = $auth->createPermission('root');
        $root->description = 'Root access';
        $auth->add($root);

        // Create roles
        $admin = $auth->createRole('admin');
        $admin->description = 'Administrator';
        $auth->add($admin);
        $auth->addChild($admin, $dashboard);
        $auth->addChild($admin, $categories);
        $auth->addChild($admin, $rbac);
        $auth->addChild($admin, $root);

        $staff = $auth->createRole('staff');
        $staff->description = 'Staff';
        $auth->add($staff);
        $auth->addChild($staff, $dashboard);
        $auth->addChild($staff, $categories);

        // Assign admin role to user 1
        $auth->assign($admin, 1);
        $auth->assign($staff, 2);

        echo "RBAC initialized successfully.\n";
    }
}
