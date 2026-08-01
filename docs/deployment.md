# Production Deployment Guide

This guide covers deploying the HRM application to production using Nginx + PHP-FPM, Redis, Queue Workers, and MySQL.

---

## Prerequisites

| Requirement | Version |
|-------------|---------|
| PHP | 8.3+ (with extensions: `pdo_mysql`, `mbstring`, `xml`, `curl`, `gd` or `imagick`, `zip`, `bcmath`, `openssl`, `tokenizer`) |
| Composer | 2.x |
| Node.js | 20+ (for asset compilation) |
| MySQL | 8.0+ |
| Redis | 7.x |
| Nginx | 1.24+ |

---

## 1. Server Preparation

### Install PHP & Extensions (Ubuntu/Debian)

```bash
sudo apt update
sudo apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring \
    php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath php8.3-gd \
    php8.3-redis composer nginx mysql-server redis-server
```

### Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## 2. Application Deployment

### Clone & Install

```bash
cd /var/www
git clone https://github.com/your-org/hrm-app.git
cd hrm-app
```

### Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
npm ci --production
npm run build
```

### Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with production values:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://hrm.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hrm_app
DB_USERNAME=hrm_user
DB_PASSWORD=strong_password

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=no-reply@yourdomain.com
MAIL_PASSWORD=smtp_password
MAIL_FROM_ADDRESS="no-reply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Important**: In production, set `APP_DEBUG=false` and `SESSION_DRIVER=redis` (or `database`) — never use `file` for sessions in a multi-server setup.

### Run Migrations & Seed

```bash
php artisan migrate --force
php artisan db:seed --force
```

### Optimize Caches

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

---

## 3. Database Setup (MySQL)

```sql
CREATE DATABASE hrm_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'hrm_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON hrm_app.* TO 'hrm_user'@'localhost';
FLUSH PRIVILEGES;
```

---

## 4. Nginx Configuration

Create `/etc/nginx/sites-available/hrm-app`:

```nginx
server {
    listen 80;
    server_name hrm.yourdomain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name hrm.yourdomain.com;

    root /var/www/hrm-app/public;
    index index.php;

    # SSL certificates (e.g., Let's Encrypt with certbot)
    ssl_certificate /etc/letsencrypt/live/hrm.yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/hrm.yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Maximum upload size (resumes, profile photos)
    client_max_body_size 10M;

    # Prevent access to hidden files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_read_timeout 300;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff2?)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/hrm-app /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

**Obtain SSL certificate:**

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d hrm.yourdomain.com
```

---

## 5. Queue Workers

Create a systemd service for queue workers. File: `/etc/systemd/system/hrm-queue.service`

```ini
[Unit]
Description=HRM Queue Worker
After=network.target redis-server.service

[Service]
User=www-data
Group=www-data
WorkingDirectory=/var/www/hrm-app
ExecStart=/usr/bin/php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=3

[Install]
WantedBy=multi-user.target
```

Enable and start:

```bash
sudo systemctl enable hrm-queue
sudo systemctl start hrm-queue
```

---

## 6. Scheduler (Cron)

Laravel's scheduler must run every minute. Add to crontab:

```bash
crontab -e
```

```
* * * * * cd /var/www/hrm-app && php artisan schedule:run >> /dev/null 2>&1
```

The scheduler handles the hourly `queue:retry --failed` command configured in `bootstrap/app.php`.

---

## 7. File Permissions

```bash
sudo chown -R www-data:www-data /var/www/hrm-app
sudo chmod -R 775 /var/www/hrm-app/storage
sudo chmod -R 775 /var/www/hrm-app/bootstrap/cache
```

---

## 8. Let's Encrypt / SSL

The Nginx config above references certificates. Use certbot to obtain and auto-renew:

```bash
sudo certbot --nginx -d hrm.yourdomain.com
sudo certbot renew --dry-run
```

---

## 9. Production Health Checks

After deployment, verify:

```bash
# Health endpoint (Laravel built-in)
curl https://hrm.yourdomain.com/up

# Route list
php artisan route:list

# Queue health
php artisan queue:monitor

# Load test (optional, before go-live)
php artisan app:load-test --iterations=10
```

---

## 10. Zero-Downtime Deployment

For zero-downtime deployments, use a deploy script like:

```bash
#!/usr/bin/env bash
set -e

cd /var/www/hrm-app

# Maintenance mode
php artisan down --retry=60

# Pull latest code
git pull origin main

# Dependencies & assets
composer install --no-dev --optimize-autoloader
npm ci --production
npm run build

# Migrations
php artisan migrate --force

# Restart queue workers
sudo systemctl restart hrm-queue

# Optimize caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Back online
php artisan up
```

---

## 11. Backup Strategy

Schedule daily MySQL dumps and storage backups:

```bash
# Daily at 2:00 AM
0 2 * * * mysqldump -u hrm_user -p'strong_password' hrm_app | gzip > /var/backups/hrm/hrm_$(date +\%F).sql.gz
0 3 * * * rsync -a /var/www/hrm-app/storage/app/ /var/backups/hrm/storage/
```

---

## 12. Monitoring & Logging

- **Application logs**: `/var/www/hrm-app/storage/logs/laravel.log`
- **Queue failures**: `php artisan queue:failed` / `php artisan queue:monitor`
- **Nginx logs**: `/var/log/nginx/hrm-app.access.log`, `/var/log/nginx/hrm-app.error.log`

Optionally install Laravel Horizon or Pulse for advanced queue/metrics monitoring in production.

---

## Related Documents

- [Architecture Guide](architecture.md)
- [Root README](../README.md)