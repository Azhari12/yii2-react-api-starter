<?php

namespace app\modules\rbac\models;

use yii\db\ActiveRecord;

class Menu extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%menu}}';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parent', 'order'], 'integer'],
            [['name', 'route', 'data'], 'string'],
        ];
    }
}
