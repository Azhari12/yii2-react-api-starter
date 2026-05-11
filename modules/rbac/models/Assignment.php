<?php

namespace app\modules\rbac\models;

use app\modules\rbac\components\Configs;
use Yii;
use yii\base\Model;

class Assignment extends Model
{
    public $id;
    public $user;

    public function __construct($id, $user = null, $config = [])
    {
        $this->id = $id;
        $this->user = $user;
        parent::__construct($config);
    }

    public function getItems()
    {
        $auth = Configs::authManager();

        $available = [];
        $assigned = [];

        $roles = $auth->getRoles();
        $permissions = $auth->getPermissions();
        $assignedItems = $auth->getAssignments($this->id);

        foreach ($roles as $name => $role) {
            if (isset($assignedItems[$name])) {
                $assigned[] = $name;
            } else {
                $available[] = $name;
            }
        }

        // Only include non-route permissions
        foreach ($permissions as $name => $permission) {
            if (strncmp($name, '/', 1) !== 0) {
                if (isset($assignedItems[$name])) {
                    $assigned[] = $name;
                } else {
                    $available[] = $name;
                }
            }
        }

        return [
            'available' => $available,
            'assigned' => $assigned,
        ];
    }

    public function assign($data)
    {
        $auth = Configs::authManager();
        $items = $data['items'] ?? [];

        foreach ($items as $name) {
            $item = $auth->getRole($name);
            if ($item === null) {
                $item = $auth->getPermission($name);
            }
            if ($item !== null) {
                try {
                    $auth->assign($item, $this->id);
                } catch (\Exception $e) {
                    // Already assigned
                }
            }
        }
        return true;
    }

    public function revoke($data)
    {
        $auth = Configs::authManager();
        $items = $data['items'] ?? [];

        foreach ($items as $name) {
            $item = $auth->getRole($name);
            if ($item === null) {
                $item = $auth->getPermission($name);
            }
            if ($item !== null) {
                $auth->revoke($item, $this->id);
            }
        }
        return true;
    }
}
