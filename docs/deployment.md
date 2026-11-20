# Production Deployment Guide

This guide describes a conventional Linux deployment with Nginx, PHP-FPM, MySQL, and a supervised Laravel queue worker. Redis is optional; the application defaults in `.env.example` use database-backed sessions, cache, and queue. Adapt service names and paths to the target host.

## Requirements

- PHP 8.3+ with the extensions required by Laravel and installed packages, including PDO for the chosen database, `mbstring`, `openssl`, `tokenizer`, XML support, and `ctype`/`json`.
- Composer 2 and Node.js/npm versions compatible with the lockfiles and Vite configuration.
- MySQL 8+ (or another database supported by Laravel and this project's migrations), Nginx, and PHP-FPM.
- A persistent queue worker and Laravel scheduler if the deployed configuration uses queues or scheduled tasks.

Use `composer.json`, `package.json`, and the lockfiles as authoritative dependency references. Build assets in CI or on the deployment host with development dependencies available to Vite.

## Prepare the host and database

Create a dedicated database and least-privilege database account using your organization's secret-management process. Restrict database network access to application hosts. Set up TLS, DNS, firewall rules, monitoring, and backups before directing production traffic.

Create an application release directory and install the source. Keep `.env` and persistent user uploads outside version control. The web server document root must be the app's `public/` directory, never the repository root.

## Configure the application

Create `.env` from `.env.example`, then set at least:

```dotenv
APP_NAME="HRM"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://hrm.example.com

APP_KEY=base64:REPLACE_WITH_GENERATED_KEY

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hrm_app
DB_USERNAME=hrm_app
DB_PASSWORD=SET_VIA_SECRET_MANAGEMENT

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=SET_VIA_SECRET_MANAGEMENT
MAIL_PASSWORD=SET_VIA_SECRET_MANAGEMENT
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

Generate `APP_KEY` once for a new environment with `php artisan key:generate`. Do not replace an existing production key casually: encrypted values and sessions may become unreadable. Keep secrets out of shell history, deployment logs, and source control. Use Redis for sessions/cache/queues only after installing and configuring the appropriate PHP client and Laravel connection.

## Install, build, and migrate

Run these commands from the release directory with production environment values available:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan storage:link
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

`npm ci --omit=dev` would omit Vite and its plugins, which are development dependencies, so do not use it before the asset build. In a CI pipeline, build assets in a separate stage and deploy the generated `public/build` directory with the release.

Do not run `db:seed` in production by default. Review each seeder first; seed only required, safe reference data through an explicit deployment procedure. Do not use demo accounts or sample credentials in a live environment.

## Nginx and PHP-FPM

Point Nginx at `public/`. Example server block (adjust PHP-FPM socket and TLS paths):

```nginx
server {
    listen 80;
    server_name hrm.example.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name hrm.example.com;
    root /var/www/hrm-app/current/public;
    index index.php;

    ssl_certificate /etc/letsencrypt/live/hrm.example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/hrm.example.com/privkey.pem;

    client_max_body_size 10M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        internal;
    }

    location ~ \.php$ { return 404; }

    location ~ /\.(?!well-known).* { deny all; }
}
```

Validate the Nginx configuration before reloading it. Keep TLS configuration current and use the platform's certificate renewal mechanism. Laravel's `public/` directory is the only intended web root.

## Files and permissions

Run PHP-FPM and queue workers as a dedicated unprivileged application account. Grant that account write access only to `storage/` and `bootstrap/cache/`; code and `.env` should not be writable by the web process. Store uploaded employee documents and resumes on a private disk unless they are intentionally public. Confirm backup, retention, and restore procedures cover both database and persistent uploads.

## Queue worker

If the queue connection is not `sync`, supervise a worker with systemd, Supervisor, or a managed process service. Example systemd service for the database queue:

```ini
[Unit]
Description=HRM Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
WorkingDirectory=/var/www/hrm-app/current
ExecStart=/usr/bin/php artisan queue:work database --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

Adjust the queue connection, PHP path, and account to match the host. Restart workers on each release so they load current code. Monitor failed jobs and verify jobs that may be retried are safe to execute more than once.

## Scheduler

The application registers an hourly failed-job retry in `bootstrap/app.php`. If using Laravel's scheduler, install the standard once-per-minute scheduler entry:

```cron
* * * * * cd /var/www/hrm-app/current && php artisan schedule:run >> /dev/null 2>&1
```

Review the retry policy before enabling it in production, since retrying failed jobs can repeat side effects if a job is not idempotent.

## Release procedure

Use immutable release directories and a `current` symlink, or an equivalent release strategy. A typical release should:

1. Fetch an identified revision and install locked dependencies.
2. Build frontend assets and validate the release in CI.
3. Put the app in maintenance mode if the deployment is not zero-downtime and the maintenance page is configured.
4. Apply backward-compatible migrations with `php artisan migrate --force`.
5. Switch the active release, clear/rebuild Laravel caches, and restart queue workers.
6. Check the health endpoint and critical workflows, then restore traffic.
7. Retain the prior release and a verified backup for rollback.

Avoid `git pull` directly in a live application directory. Database migrations need a compatible rollback plan; reverting application files alone may not reverse a schema change.

## Operations checklist

- Confirm `/up` responds over HTTPS and `APP_DEBUG=false`.
- Check `php artisan route:list` and confirm the expected routes/middleware after deployment.
- Monitor application logs, PHP-FPM/Nginx, queue depth, failed jobs, disk space, and database health.
- Test database and uploaded-file restoration periodically, not only backup creation.
- Keep framework and Composer/npm dependencies patched through a reviewed update process.
- Configure retention and access controls for employee data, candidate records, audit logs, and backups.

For local commands see [development.md](development.md); for application boundaries see [architecture.md](architecture.md); for testing see [testing.md](testing.md).
