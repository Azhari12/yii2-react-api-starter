<?php

namespace app\models;

use Yii;

/**
 * Model untuk tabel sso.akn_user (Akun pengguna dari sistem SSO).
 *
 * @property int         $userid
 * @property int|null    $id_pegawai
 * @property string      $username
 * @property string      $password
 * @property string|null $nama
 * @property string|null $tanggal_pendaftaran
 * @property string|null $role
 * @property string|null $token_aktivasi
 * @property int|null    $status  0 = aktif, 1 = non-aktif, 2 = blokir
 */
class AkunAknUser extends \yii\db\ActiveRecord
{
    /**
     * Gunakan koneksi database utama (db) karena schema sso ada di DB yang sama.
     */
    public static function getDb()
    {
        return \Yii::$app->db;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sso.akn_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_pegawai', 'username', 'password'], 'required'],
            [['id_pegawai', 'status'], 'default', 'value' => null],
            [['id_pegawai', 'status'], 'integer'],
            [['tanggal_pendaftaran'], 'safe'],
            [['token_aktivasi'], 'string'],
            [['username', 'nama', 'role'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 100],
        ];
    }

    // =========================================================================
    // Getter Methods
    // =========================================================================

    public function getUserid()
    {
        return $this->userid;
    }

    public function getIdPegawai()
    {
        return $this->id_pegawai;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getNama()
    {
        return $this->nama;
    }

    public function getTanggalPendaftaran()
    {
        return $this->tanggal_pendaftaran;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getStatus()
    {
        return $this->status;
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    /**
     * Cek apakah akun sedang aktif (status = 0 berarti aktif di sistem SSO).
     */
    public function isAktif()
    {
        return $this->status == '0';
    }

    /**
     * Cari user berdasarkan username dengan status aktif.
     */
    public static function getOneKodeAkun($kodeAkun, $includeNonActive = false)
    {
        if (is_null($kodeAkun)) {
            return null;
        }

        $query = ['username' => $kodeAkun];
        if (!$includeNonActive) {
            $query['status'] = '0';
        }

        return self::findOne($query);
    }
}
