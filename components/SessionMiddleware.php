<?php

namespace app\components;

use Yii;
use DateTime;
use yii\base\Component;

class SessionMiddleware extends Component
{
    public function checkSession()
    {
        $app = Yii::$app;

        // Lewati request OPTIONS (CORS preflight)
        if ($app->request->method === 'OPTIONS') {
            return;
        }

        // Lewati route auth/* agar proses cek login tidak loop
        $route = $app->request->resolve()[0] ?? '';
        if (strpos($route, 'auth/') === 0 || strpos($route, 'debug/') === 0 || strpos($route, 'gii/') === 0) {
            return;
        }

        $user = $app->user;

        // Jika belum login, biarkan middleware lain yang handle
        if ($user->isGuest) {
            return;
        }

        // Ambil identitas user yang sedang login
        $identity = $user->identity;

        // Cek apakah sesi sudah kadaluarsa berdasarkan batasWaktu
        if ($identity && !empty($identity->kodeSesi)) {
            // Session masih valid, tidak perlu action apa-apa
            // Batas waktu sudah dicek oleh cekStatus() di Sesi model
        }
    }
}
