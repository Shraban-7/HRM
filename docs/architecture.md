# Application Architecture

## Overview

This project is a Laravel 13 modular monolith on PHP 8.3+. It uses Laravel's web stack with Blade views, Tailwind CSS 4, jQuery, Vite, Eloquent, and dompdf. Domain code is grouped by HRM area while sharing one application, database, and deployment unit.

## Request and domain flow

```text
Browser → routes/web.php → middleware → controller → request validation
                                               ↓
                                  service / repository / model
                                               ↓
                                       database and views
                                               ↓
                           event, listener, notification, or job
```

Controllers coordinate HTTP concerns. Form Requests validate supported inputs. Services implement domain rules and calculations. Eloquent models represent persisted records and relationships. The repository pattern is used for selected persistence concerns; it is not a mandatory layer for every model or controller.

## Code map

| Path | Responsibility |
|---|---|
| `routes/web.php` | Web endpoints, route names, and middleware assignments. |
| `app/Http/Controllers/` | HTTP orchestration and response selection, grouped by feature. |
| `app/Http/Requests/` | Dedicated request validation where present. |
| `app/Services/` | Authentication, employee, payroll, attendance, benefits, and other domain logic. |
| `app/Repositories/Contracts/` | Interfaces for selected repository operations. |
| `app/Repositories/Eloquent/` | Eloquent-backed repository implementations. |
| `app/Repositories/Cache/` | Cached decorators for employee and payroll repositories. |
| `app/Models/` | Eloquent models and relationships. |
| `app/Policies/`, `app/Middleware/` | Authorization and cross-cutting request controls. |
| `app/Events/`, `app/Listeners/`, `app/Jobs/` | Domain events, side effects, and background-capable work. |
| `app/Notifications/` | Laravel notifications. |
| `database/migrations/`, `database/seeders/` | Schema evolution and initial/demo data. |
| `resources/views/`, `resources/js/`, `resources/css/` | Server-rendered UI and frontend assets. |

## Persistence and repositories

Repository contracts currently cover employee, payroll, and attendance operations. `RepositoryServiceProvider` binds employee and payroll interfaces to cache decorators when the configured cache store is `redis` or `database`; other stores use the Eloquent repository directly. Attendance uses its Eloquent repository without a cache decorator because it represents time-sensitive records.

The cache decorators use versioned cache namespaces to invalidate groups of cached reads after writes. When changing repository behavior, preserve invalidation semantics and consider consistency across multiple application instances. Laravel's database and cache configuration remains the source of truth for connections and stores.

## Services, events, and jobs

Services contain calculations and lifecycle rules such as payroll/tax computation, attendance and leave handling, benefit costs, employee lifecycle operations, and org chart construction. Keep financial calculations isolated and deterministic where practical; persist related changes transactionally.

`EventServiceProvider` explicitly maps events to listeners and disables event auto-discovery. Current mappings include employee hire/termination, salary change, payroll completion, leave approval, performance review submission, and benefit enrollment changes. Review the event provider and listener implementations before assuming a side effect runs asynchronously: a listener only runs in the queue when it is configured to queue.

Jobs include payroll processing, payslip generation, report generation, and email delivery. The default queue connection in `.env.example` is `database`; a worker must be supervised in deployments that rely on queued work. A scheduled hourly retry of failed jobs is registered in `bootstrap/app.php`; assess idempotency and failure handling before changing retry behavior.

## Authentication and authorization

The route file applies the `custom.auth` middleware to protected application routes. Public routes include authentication pages and careers endpoints. Role and permission middleware are registered in `bootstrap/app.php`; selected endpoints also use Laravel policies. Authorization coverage is route-specific, so review each endpoint and its controller/policy before exposing or changing an action. Never treat a hidden button as an access control.

## Configuration and testing

Application configuration lives in `config/`; environment-specific values come from `.env`. `.env.example` uses SQLite and database-backed session, cache, and queue defaults. PHPUnit overrides these with an in-memory SQLite database, array cache/session, and synchronous queue in `phpunit.xml`.

The suite is divided into `tests/Feature/` and `tests/Unit/`. See [testing.md](testing.md) for focused commands and guidance. See [development.md](development.md) for setup, and [deployment.md](deployment.md) for production operations.
