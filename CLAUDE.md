# Claude Code Guide

## Project

This repository is a Laravel 13 HRM application running on PHP 8.3+. It uses Blade, Tailwind CSS 4, jQuery, Vite, Eloquent, and dompdf. The application is a single Laravel monolith; domain behavior is organized into services, with repository abstractions used for employee, attendance, and payroll persistence.

Read [README.md](README.md) for setup and [docs/README.md](docs/README.md) for the documentation index. Follow the detailed conventions in [docs/architecture.md](docs/architecture.md).

## Important paths

- `routes/web.php` — web routes and middleware boundaries.
- `app/Http/Controllers/` — request orchestration and response handling.
- `app/Http/Requests/` — reusable request validation.
- `app/Services/` — HR domain rules and calculations.
- `app/Repositories/` — repository contracts, Eloquent adapters, and cache decorators.
- `app/Models/` — Eloquent models and relationships.
- `app/Policies/`, `app/Middleware/` — authorization and request security.
- `app/Jobs/`, `app/Events/`, `app/Listeners/` — asynchronous work and domain side effects.
- `database/migrations/`, `database/seeders/` — schema and initial data.
- `resources/views/`, `resources/js/`, `resources/css/` — user interface.
- `tests/Feature/`, `tests/Unit/` — automated tests.

## Change guidelines

1. Inspect the relevant route, controller, service, model, migration, view, and tests before editing. Keep business rules in services and controllers focused on orchestration.
2. Validate untrusted input with Form Requests or explicit Laravel validation. Enforce access at the route and policy/service boundary as appropriate; do not rely on hidden UI controls.
3. Treat payroll, bank details, employee personal data, leave decisions, and audit records as sensitive. Avoid logging secrets or unnecessary personal data. Preserve transaction boundaries and authorization checks.
4. Follow existing Laravel conventions, PSR-4 namespaces, Blade patterns, and the current Tailwind/Vite setup. Avoid adding dependencies for functionality already available in Laravel.
5. Add or update focused tests for behavior changes. Do not run the test suite unless the user asks for verification; if tests are run, report the exact command and outcome.
6. Keep migrations forward-compatible and never edit an already-applied migration to change production schema. Use a new migration for schema changes.
7. Update the relevant documentation when behavior, setup, permissions, or operations change. Never commit `.env`, credentials, generated user data, or production secrets.

## Useful commands

```bash
composer install
npm install
php artisan migrate --seed
composer dev
php artisan test
npm run build
```

Use the test/build commands only when requested or when the task explicitly calls for verification. Refer to [docs/development.md](docs/development.md) and [docs/testing.md](docs/testing.md) for details.

## Domain areas

The application contains authentication/RBAC, employee and department management, payroll/tax/payslips, attendance/shifts/leave, recruitment, performance, benefits/COBRA, notifications, reports, and audit logging. Check current routes and implementation before assuming a workflow or permission exists; documentation and feature breadth do not replace code-level verification.
