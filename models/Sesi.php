<?php

namespace app\models;

use Yii;
use yii\helpers\Html;

/**
 * Model untuk tabel sso.akn_session (Sesi login dari sistem SSO).
 *
 * Kolom-kolom tabel:
 * @property int    $id
 * @property string $tgb  Tanggal buat sesi
 * @property string $bts  Batas waktu sesi
 * @property string $kds  Kode sesi (token unik, dipakai sebagai authKey)
 * @property int    $ida  ID akun (FK ke sso.akn_user.userid)
 * @property string $ipa  IP address saat login
 * @property string $inf  Informasi user agent
 * @property string $tat  Tanggal akses terakhir
 * @property string $isk  Is Keluar: '0' = aktif, '1' = sudah logout
 */
class Sesi extends \yii\db\ActiveRecord
{
    private $_akun = false;

    /**
     * Gunakan koneksi database utama (schema sso ada di DB yang sama).
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
        return 'sso.akn_session';
    }

    /**
     * Relasi ke model AkunAknUser (profil pengguna).
     */
    public function getAkun()
    {
        if ($this->_akun === false) {
            $this->_akun = AkunAknUser::findOne([
                'userid' => $this->ida,
                'status' => '0', // hanya akun yang aktif
            ]);
        }
        return $this->_akun;
    }

    // =========================================================================
    // Getter Methods
    // =========================================================================

    public function getId()
    {
        return $this->id;
    }

    public function getBatasSesi()
    {
        return $this->bts;
    }

    public function getKodeSesi()
    {
        // Html::encode untuk keamanan XSS
        return Html::encode($this->kds);
    }

    public function getIdAkun()
    {
        return $this->ida;
    }

    public function getIpAddress()
    {
        return $this->ipa;
    }

    public function getTanggalBuat()
    {
        return $this->tgb;
    }

    public function getIsKeluar()
    {
        return $this->isk;
    }

    // =========================================================================
    // Status Methods
    // =========================================================================

    public function isKeluar()
    {
        return $this->isk == '1';
    }

    public function isBerlaku()
    {
        return strtotime($this->bts) > time();
    }

    /**
     * Cek apakah sesi masih valid dan perbarui waktu akses terakhir.
     */
    public function cekStatus()
    {
        if ($this->isKeluar()) {
            return false;
        }
        // Update tanggal akses terakhir
        $this->tat = date('Y-m-d H:i:s');
        if (!$this->save(false)) {
            return false;
        }
        return true;
    }

    // =========================================================================
    // Rules & Labels
    // =========================================================================

    public function rules()
    {
        return [
            [['tgb', 'bts', 'kds', 'ida', 'ipa', 'inf', 'tat'], 'safe'],
            [['ida'], 'default', 'value' => null],
            [['ida'], 'integer'],
            [['inf'], 'string'],
            [['kds'], 'string', 'max' => 64],
            [['ipa'], 'string', 'max' => 30],
            [['isk'], 'string', 'max' => 1],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'  => 'ID',
            'tgb' => 'Tanggal Buat',
            'bts' => 'Batas Waktu',
            'kds' => 'Kode Sesi',
            'ida' => 'ID Akun',
            'ipa' => 'IP Address',
            'inf' => 'Informasi',
            'tat' => 'Tanggal Akses',
            'isk' => 'Is Keluar',
        ];
    }
}
