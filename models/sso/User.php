<?php

namespace app\models\sso;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public static function getDb()
    {
        return Yii::$app->db_sso;
    }

    public static function tableName()
    {
        return 'so.akn_user';
    }

    public static function findIdentity($id)
    {
        return static::findOne(['userid' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    public function getId()
    {
        return $this->userid;
    }

    public function getAuthKey()
    {
        return $this->auth_key ?? '';
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    {
        return md5($password) === $this->password;
    }
}
