<?php

namespace app\models;

use Yii;
use yii\base\BaseObject;
use yii\web\IdentityInterface;

class Identitas extends BaseObject implements IdentityInterface
{
    public $id;
    public $username;
    public $nama;
    public $roles;
    public $authKey;

    public static function findIdentity($id)
    {
        $sesi = Sesi::findOne(['userid' => $id]);
        if ($sesi) {
            return new static([
                'id' => $sesi->userid,
                'username' => $sesi->username,
                'nama' => $sesi->nama,
                'roles' => $sesi->roles ?? '',
                'authKey' => $sesi->auth_key ?? '',
            ]);
        }
        return null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }
}
