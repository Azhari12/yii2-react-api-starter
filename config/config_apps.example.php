<?php
/**
 * Configuration file for Yii2 React API Starter
 *
 * SETUP:
 * 1. Copy file ini ke config_apps.php
 * 2. Sesuaikan semua nilai di bawah dengan environment Anda
 * 3. Pastikan config_apps.php TIDAK masuk ke git (sudah ada di .gitignore)
 */
return [
    'config' => [
        'db' => [
            'postgre' => [
                'simrs' => [
                    // DSN PostgreSQL: host, port, nama database
                    'db_pg'   => 'pgsql:host=localhost;port=5432;dbname=your_database',
                    'user_pg' => 'postgres',
                    'pass_pg' => 'your_password',
                ],
            ],
        ],
        'url_apps' => [
            // URL SSO eksternal (akhiri dengan /). Kosongkan jika tidak pakai SSO.
            'sso'  => 'http://sso.yourdomain.com/resikaa-sso/web/',
            // URL aplikasi frontend
            'base' => 'http://app.yourdomain.com:5173/',
            // URL aplikasi backend API
            'api'  => 'http://api.yourdomain.com:8080/',
        ],
        /**
         * Cookie domain untuk SSO cross-subdomain.
         * Gunakan format .yourdomain.com (dengan titik di depan).
         * Contoh: '.starter.aa' untuk subdomain lokal.
         */
        'cookie_domain' => '.yourdomain.com',
        /**
         * Cookie validation key — HARUS SAMA dengan nilai di aplikasi SSO Anda.
         * Jika tidak pakai SSO, isi dengan string acak yang unik.
         */
        'cookie_validation_key' => 'your-secret-key-here',
    ],
];
