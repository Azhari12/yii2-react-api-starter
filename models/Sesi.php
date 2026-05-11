<?php

namespace app\models;

use yii\db\ActiveRecord;

class Sesi extends ActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->db_sso;
    }

    public static function tableName()
    {
        return 'so.akn_user';
    }

    public function rules()
    {
        return [];
    }
}
