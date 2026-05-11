<?php

namespace app\components;

use Yii;
use yii\base\Component;

class ApiComponent extends Component
{
    public $baseUrl;

    public function init()
    {
        parent::init();
        if ($this->baseUrl === null) {
            $this->baseUrl = Yii::$app->params['config_apps']['config']['url_apps']['api'] ?? '';
        }
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }
}
