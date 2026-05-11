<?php

namespace app\modules\rbac\models;

use app\modules\rbac\components\Configs;
use Yii;
use yii\base\Model;
use yii\rbac\Item;

class AuthItem extends Model
{
    public $name;
    public $type;
    public $description;
    public $ruleName;
    public $data;
    public $item;

    private $_oldName;

    public function __construct($item = null, $config = [])
    {
        $this->item = $item;
        if ($item !== null) {
            $this->name = $item->name;
            $this->type = $item->type;
            $this->description = $item->description;
            $this->ruleName = $item->ruleName;
            $this->data = $item->data;
            $this->_oldName = $item->name;
        }
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'description', 'ruleName'], 'string'],
            [['name'], 'match', 'pattern' => '/^[a-zA-Z0-9_\-\/\*@]+$/'],
        ];
    }

    public function load($data, $formName = null)
    {
        if (isset($data['AuthItem'])) {
            $data = $data['AuthItem'];
        }
        $this->name = $data['name'] ?? $this->name;
        $this->description = $data['description'] ?? $this->description;
        $this->ruleName = $data['ruleName'] ?? $this->ruleName;
        return true;
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $auth = Configs::authManager();

        if ($this->item === null) {
            // Create new
            if ($this->type == Item::TYPE_ROLE) {
                $item = $auth->createRole($this->name);
            } else {
                $item = $auth->createPermission($this->name);
            }
            $item->description = $this->description ?? '';
            $item->ruleName = $this->ruleName;
            $item->data = $this->data;
            return $auth->add($item);
        } else {
            // Update existing
            $item = $this->item;
            $item->name = $this->name;
            $item->description = $this->description ?? '';
            $item->ruleName = $this->ruleName;
            $item->data = $this->data;
            return $auth->update($this->_oldName, $item);
        }
    }

    public function getItems()
    {
        $auth = Configs::authManager();
        $advanced = Configs::instance()->advanced;

        $available = [];
        $assigned = [];

        if ($this->type == Item::TYPE_ROLE) {
            $children = $auth->getChildren($this->name);
            $allItems = array_merge($auth->getRoles(), $auth->getPermissions());
        } else {
            $children = $auth->getChildren($this->name);
            $allItems = array_merge($auth->getPermissions(), $auth->getRoles());
            // For permissions, also include routes
            foreach ($auth->getPermissions() as $name => $perm) {
                if (strncmp($name, '/', 1) === 0) {
                    $allItems[$name] = $perm;
                }
            }
        }

        foreach ($allItems as $name => $item) {
            if ($name === $this->name) continue;
            if (isset($children[$name])) {
                $assigned[] = $name;
            } else {
                $available[] = $name;
            }
        }

        return [
            'available' => $available,
            'assigned' => $assigned,
        ];
    }

    public function addChildren($data)
    {
        $auth = Configs::authManager();
        $items = $data['items'] ?? [];

        foreach ($items as $name) {
            $child = $auth->getPermission($name);
            if ($child === null) {
                $child = $auth->getRole($name);
            }
            if ($child !== null) {
                try {
                    $auth->addChild($this->item, $child);
                } catch (\Exception $e) {
                    // Skip if already exists
                }
            }
        }
        return true;
    }

    public function removeChildren($data)
    {
        $auth = Configs::authManager();
        $items = $data['items'] ?? [];

        foreach ($items as $name) {
            $child = $auth->getPermission($name);
            if ($child === null) {
                $child = $auth->getRole($name);
            }
            if ($child !== null) {
                $auth->removeChild($this->item, $child);
            }
        }
        return true;
    }
}
