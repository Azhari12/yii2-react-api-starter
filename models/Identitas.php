<?php

namespace app\models;

use Yii;
use yii\base\BaseObject;
use yii\web\IdentityInterface;

/**
 * Kelas Identitas pengguna yang terintegrasi dengan SSO.
 *
 * Kelas ini membungkus objek Sesi (sso.akn_session) dan menarik
 * data profil dari AkunAknUser (sso.akn_user) melalui relasi.
 *
 * Alur auto-login via cookie:
 *   1. Yii2 membaca cookie _identity-id → dapat [userId, authKey, duration]
 *   2. Memanggil findIdentity($userId) → mencari sesi aktif di DB
 *   3. Memanggil validateAuthKey($authKey) → cocokkan dengan kds di sesi
 */
class Identitas extends BaseObject implements IdentityInterface
{
    public $id;
    public $username;
    public $nama;
    public $roles;
    public $kodeSesi;  // Ini adalah authKey (kds dari sso.akn_session)

    private $_sesi = null;

    /**
     * Cari identitas berdasarkan userId dari cookie.
     * Query ke sso.akn_session untuk menemukan sesi aktif.
     */
    public static function findIdentity($id)
    {
        // Cari sesi yang aktif (belum logout) untuk user ini
        $sesi = Sesi::find()
            ->where([
                'ida' => $id,
                'isk' => '0',  // isk = is keluar, '0' berarti masih aktif
            ])
            ->orderBy('tgb DESC')
            ->limit(1)
            ->one();

        if (is_null($sesi)) {
            return null;
        }

        // Pastikan sesi masih berlaku dan akun masih aktif
        $akun = $sesi->getAkun();
        if (is_null($akun)) {
            return null;
        }

        return new static([
            'id'       => $akun->getUserid(),
            'username' => $akun->getUsername(),
            'nama'     => $akun->getNama(),
            'roles'    => $akun->getRole(),
            'kodeSesi' => $sesi->getKodeSesi(),
        ]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * authKey yang digunakan Yii2 adalah kodeSesi (nilai kds dari tabel sesi).
     * Inilah yang disimpan SSO ke dalam cookie saat login.
     */
    public function getAuthKey()
    {
        return $this->kodeSesi;
    }

    /**
     * Validasi bahwa kodeSesi di cookie cocok dengan yang ada di database.
     */
    public function validateAuthKey($authKey)
    {
        return $this->kodeSesi === $authKey;
    }
}
