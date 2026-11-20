# Development Setup

## Requirements

- PHP 8.3 or newer, Composer 2, Node.js compatible with the Vite version in `package-lock.json`, and npm.
- A supported Laravel database driver. SQLite is the default in `.env.example`; MySQL and PostgreSQL can be configured through Laravel's database environment variables.
- PHP extensions required by Laravel and the installed Composer packages. Check `composer.json` and the active PHP installation if Composer reports a missing extension.

## First run

From the repository root:

```bash
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
```

On macOS/Linux, replace the `Copy-Item` command with `cp .env.example .env`. Configure the database and mail settings in `.env` before migrating or exercising workflows that send mail. Never commit `.env`.

The Composer `setup` script is also available, but it migrates the configured database. Review `.env` first so the command does not target an unintended database.

## Local development

Run the coordinated development processes with:

```bash
composer dev
```

The script starts Laravel's development server, queue listener, log tailer, and Vite. To start processes separately, use `php artisan serve`, `php artisan queue:listen`, and `npm run dev` in separate terminals.

## Configuration notes

- `.env.example` is the tracked configuration template. Laravel configuration is under `config/`.
- The default queue and cache drivers are database-backed. Set `QUEUE_CONNECTION` or `CACHE_STORE` to a production-ready service when required by deployment.
- The default mailer logs messages. Configure a real mail transport for password resets and notifications outside development.
- Uploaded files use Laravel's configured filesystem disk. Configure storage and access deliberately for production.
- Seeders may create demonstration accounts or data. Inspect `database/seeders/` before seeding a shared or production environment.

## Useful commands

```bash
php artisan route:list
php artisan migrate:status
php artisan queue:work
php artisan config:clear
npm run build
```

Run migrations only against the intended database. `php artisan migrate:fresh` drops existing tables and must not be used on databases with data to preserve.
