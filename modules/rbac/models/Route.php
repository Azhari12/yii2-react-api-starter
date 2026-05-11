<?php

namespace app\modules\rbac\models;

use app\modules\rbac\components\Configs;
use Yii;
use yii\base\Model;
use yii\caching\TagDependency;

class Route extends Model
{
    public function getRoutes()
    {
        $auth = Configs::authManager();
        $routes = [];

        // Get all registered routes from the application
        $appRoutes = $this->getAppRoutes();

        // Get assigned routes (already in auth system)
        $assignedRoutes = [];
        foreach ($auth->getPermissions() as $name => $permission) {
            if (strncmp($name, '/', 1) === 0) {
                $assignedRoutes[$name] = $name;
            }
        }

        // Available = app routes not yet assigned
        $available = [];
        foreach ($appRoutes as $route) {
            if (!isset($assignedRoutes[$route])) {
                $available[] = $route;
            }
        }

        sort($available);
        $assigned = array_values($assignedRoutes);
        sort($assigned);

        return [
            'available' => $available,
            'assigned' => $assigned,
        ];
    }

    public function addNew($data)
    {
        $auth = Configs::authManager();
        $routes = $data['routes'] ?? $data['items'] ?? [];

        foreach ($routes as $route) {
            $r = $auth->createPermission($route);
            $r->description = 'Route: ' . $route;
            try {
                $auth->add($r);
            } catch (\Exception $e) {
                // Already exists
            }
        }
    }

    public function remove($data)
    {
        $auth = Configs::authManager();
        $routes = $data['routes'] ?? $data['items'] ?? [];

        foreach ($routes as $route) {
            $item = $auth->getPermission($route);
            if ($item) {
                $auth->remove($item);
            }
        }
    }

    private function getAppRoutes()
    {
        $routes = [];
        $this->getRouteRecursive(Yii::$app, $routes);
        return array_unique($routes);
    }

    private function getRouteRecursive($module, &$routes, $prefix = '')
    {
        $token = "Get Route of '" . get_class($module) . "' with prefix '$prefix'";
        Yii::beginProfile($token, __METHOD__);

        foreach ($module->getModules() as $id => $child) {
            if (($child = $module->getModule($id)) !== null) {
                $this->getRouteRecursive($child, $routes, $prefix . '/' . $id);
            }
        }

        $namespace = trim($module->controllerNamespace, '\\') . '\\';
        $path = Yii::getAlias('@' . str_replace('\\', '/', $namespace));

        if (is_dir($path)) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            $iterator = new \RegexIterator($iterator, '/^.+Controller\.php$/i', \RecursiveRegexIterator::GET_MATCH);

            foreach ($iterator as $matches) {
                $file = $matches[0];
                $relativePath = str_replace([$path, 'Controller.php', '\\'], ['', '', '/'], $file);
                $controllerId = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', '-\0', ltrim($relativePath, '/')));
                $controllerId = ltrim($controllerId, '-');

                $className = $namespace . str_replace(['/', '.php'], ['\\', ''], str_replace($path, '', $file));
                if (class_exists($className)) {
                    $class = new \ReflectionClass($className);
                    foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                        if (strpos($method->getName(), 'action') === 0 && $method->getName() !== 'actions') {
                            $actionId = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', '-\0', substr($method->getName(), 6)));
                            $actionId = ltrim($actionId, '-');
                            $route = $prefix . '/' . $controllerId . '/' . $actionId;
                            $routes[] = $route;
                        }
                    }
                }
            }
        }

        Yii::endProfile($token, __METHOD__);
    }
}
