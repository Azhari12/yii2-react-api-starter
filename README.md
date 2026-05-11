# Yii2 React API Starter Kit

REST API backend for React frontend, using cookie-based session auth (SSO RESIKAA).

## Requirements

- PHP >= 7.4
- PostgreSQL 12+
- Composer
- Apache/Nginx with mod_rewrite

## Quick Start

### 1. Install Dependencies

```bash
composer install
```

### 2. Configure Database

```bash
cp config/config_apps.example.php config/config_apps.php
```

Edit `config/config_apps.php` with your database credentials and URLs.

### 3. Setup Database

Run the SQL schema:

```bash
psql -U postgres -d starter_db -f sql/schema.sql
```

Or use Docker:

```bash
docker-compose up -d
```

### 4. Initialize RBAC (Alternative to SQL)

```bash
php yii rbac/init
```

### 5. Configure Virtual Host

Point `api.starter.aa` to the `web/` directory.

Apache example:
```apache
<VirtualHost *:80>
    ServerName api.starter.aa
    DocumentRoot "D:/path/to/yii2-react-api-starter/web"
    <Directory "D:/path/to/yii2-react-api-starter/web">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 6. Add to hosts file

```
127.0.0.1 api.starter.aa
127.0.0.1 app.starter.aa
127.0.0.1 sso.starter.aa
```

## Architecture

```
controllers/        - Only auth endpoints (login check, logout)
modules/api/        - All API endpoints (RBAC, Dashboard, CRUD)
modules/rbac/       - RBAC management module
models/sso/         - SSO-related models
models/example/     - Example domain models
components/         - Shared components (Helper, Akun, SessionMiddleware)
```

## API Endpoints

### Auth
- `GET /auth/check-login` - Check authentication status
- `POST /auth/logout` - Logout

### Dashboard
- `GET /api/dashboard/summary` - Dashboard summary
- `GET /api/dashboard/stats` - Monthly statistics

### Categories (Example CRUD)
- `GET /api/category` - List (paginated)
- `GET /api/category/{id}` - View
- `POST /api/category` - Create
- `PUT /api/category/{id}` - Update
- `DELETE /api/category/{id}` - Delete

### RBAC
- `GET /api/rbac/routes` - List routes
- `POST /api/rbac/assign-routes` - Assign routes
- `GET /api/rbac/roles` - List roles
- `POST /api/rbac/create-role` - Create role
- `GET /api/rbac/permissions` - List permissions
- `POST /api/rbac/create-permission` - Create permission
- `GET /api/rbac/get-user-assignment` - List users with assignments
- `POST /api/rbac/assign-user/{id}` - Assign role/permission to user

## Default Credentials

- **Admin**: admin / admin123
- **User 1**: user1 / user123
- **User 2**: user2 / user123

## CORS

Configured for cookie-based cross-origin requests. Frontend origin is set in `config/params.php`.

## License

BSD-3-Clause
