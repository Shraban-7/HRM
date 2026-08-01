# HRM Application Architecture

## Overview

The HRM (Human Resource Management) application is a modular Laravel monolith that combines classic MVC structure with a Service Layer and Repository Pattern. This document describes the core architectural decisions, design patterns, and conventions used across the codebase.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 13.x (PHP 8.3+) |
| Frontend | Blade + Tailwind CSS v4 + jQuery 3.7.x |
| Build Tool | Vite 8 |
| Database | MySQL 8 / PostgreSQL 15 / SQLite (testing) |
| Cache | Database driver by default; Redis supported (`CACHE_STORE=redis`) |
| Queue | Database driver by default; Redis supported (`QUEUE_CONNECTION=redis`) |
| PDF Generation | dompdf/dompdf ^3.1 |

---

## Directory Structure

```
app/
├── Console/Commands/       # Artisan commands (load test, queue monitor)
├── Events/                 # Domain events (EmployeeHired, PayrollProcessed, ...)
├── Helpers/                # Standalone helper functions (HashHelper, EncryptionHelper)
├── Http/
│   ├── Controllers/        # Thin orchestration layer (validation + delegation)
│   ├── Middleware/         # CustomAuth, Role, Permission, AuditLog, SecurityHeaders
│   └── Requests/           # Form Request validation classes
├── Jobs/                   # Queue jobs (ProcessPayroll, GeneratePayslips, ...)
├── Listeners/              # Event listeners (SendWelcomeEmail, LogAuditEvent, ...)
├── Models/                 # Eloquent models (User, Employee, Payroll, ...)
├── Policies/               # Authorization policies (EmployeePolicy, PayrollPolicy, ...)
├── Providers/              # AppServiceProvider, EventServiceProvider, RepositoryServiceProvider
├── Repositories/
│   ├── Cache/              # Cache decorators wrapping Eloquent repositories
│   ├── Contracts/          # Repository interfaces
│   └── Eloquent/           # Eloquent-based implementations
└── Services/               # Domain business logic
    ├── Attendance/         # AttendanceProcessor, LeaveCalculator
    ├── Auth/               # CustomAuthService, PasswordService, TokenService
    ├── Benefits/           # BenefitCostCalculator, CobraService, Providers
    ├── Employee/           # EmployeeService, OrgChartService
    ├── Notification/       # EmailNotificationService, InAppNotificationService
    ├── Payroll/            # PayrollCalculator, TaxEngine, PayslipGenerator
    └── Performance/        # PerformanceBonusCalculator
```

---

## Layered Architecture

The application follows a strict layered architecture with one-directional dependencies:

```
┌─────────────────────────────────────────────────┐
│                   Routes (web.php)              │
├─────────────────────────────────────────────────┤
│                Controllers (thin)               │
│         Validation + Orchestration only         │
├─────────────────────────────────────────────────┤
│                  Services (fat)                 │
│           Business logic & domain rules         │
├─────────────┬───────────────┬───────────────────┤
│ Repositories│  Events/Jobs  │  Policies/Gates   │
│  (data)     │  (side effects)│  (authorization)  │
├─────────────┴───────────────┴───────────────────┤
│                Eloquent Models                  │
├─────────────────────────────────────────────────┤
│                 Database (MySQL)                │
└─────────────────────────────────────────────────┘
```

### Dependency Rules

- **Controllers** depend on Services, never directly on Eloquent query logic for business operations.
- **Services** contain domain rules and may use Repositories for persistence.
- **Repositories** abstract data access behind interfaces; Cache decorators wrap them transparently.
- **Events/Listeners** handle side effects (emails, audit logs, org chart cache invalidation).
- **Jobs** offload heavy/expensive operations (payroll processing, PDF generation, bulk email) to the queue.

---

## Repository Pattern

All data access for core entities is abstracted behind interfaces located in `app/Repositories/Contracts/`.

### Interfaces

| Interface | Implementation | Cache Decorator |
|-----------|---------------|-----------------|
| `EmployeeRepositoryInterface` | `Eloquent\EmployeeRepository` | `Cache\CachedEmployeeRepository` |
| `PayrollRepositoryInterface` | `Eloquent\PayrollRepository` | `Cache\CachedPayrollRepository` |
| `AttendanceRepositoryInterface` | `Eloquent\AttendanceRepository` | — (real-time data, no cache) |

### Binding Strategy

`RepositoryServiceProvider` binds interfaces to implementations at runtime:

- If `CACHE_STORE` is `redis` or `database`, the cache decorator wraps the Eloquent implementation.
- Otherwise, the plain Eloquent implementation is used.

The cache decorators use a **version-namespace invalidation strategy**: all employee cache keys are prefixed with a namespace version (`employees:v{n}:...`), and mutating operations (create/update/delete) increment the version counter, instantly invalidating all stale entries without needing to enumerate keys.

---

## Service Layer

Business logic lives in services under `app/Services/`. Services are registered as singletons in `AppServiceProvider::register()`.

### Key Services

| Service | Responsibilities |
|---------|-----------------|
| `EmployeeService` | Employee lifecycle: create (user + employee), update, terminate; fires `EmployeeHired`, `SalaryChanged`, `EmployeeTerminated` events |
| `PayrollCalculator` | Payroll run orchestration: iterates employees, computes gross/deductions/net via `TaxEngine`, creates payslips; fires `PayrollProcessed` |
| `TaxEngine` | Progressive tax bracket calculation using `config/hrm.php` brackets |
| `PayslipGenerator` | PDF payslip generation (dompdf) and batch generation |
| `AttendanceProcessor` | Clock-in/out, overtime calculation, period queries |
| `LeaveCalculator` | Working-day calculation, leave balances, accrual caps, eligibility |
| `EmailNotificationService` | Email dispatch for welcome, password reset, leave status, payroll notifications |
| `InAppNotificationService` | In-app notification persistence (native Laravel notifications) |
| `BenefitCostCalculator` | Benefits cost aggregation and deduction calculation |
| `CobraService` | COBRA coverage lifecycle management |
| `OrgChartService` | Department hierarchy tree building |

---

## Event Architecture

Events are dispatched by services after state changes and handled asynchronously by queued listeners where appropriate.

### Event Listener Mapping (`EventServiceProvider`)

| Event | Listeners |
|-------|-----------|
| `EmployeeHired` | `SendWelcomeEmail`, `UpdateOrgChart`, `LogAuditEvent` |
| `EmployeeTerminated` | `LogAuditEvent`, `UpdateOrgChart` |
| `SalaryChanged` | `LogAuditEvent` |
| `PayrollProcessed` | `NotifyPayrollTeam`, `LogAuditEvent` |
| `LeaveApproved` | `LogAuditEvent` |
| `PerformanceReviewSubmitted` | `LogAuditEvent` |
| `BenefitEnrollmentChanged` | `LogAuditEvent`, `OfferCobraCoverage` |

### Listener Pattern

- **LogAuditEvent** writes a structured audit trail for every domain event.
- **UpdateOrgChart** invalidates/rebuilds the org chart cache when employee structure changes.
- **SendWelcomeEmail** sends onboarding email via the queued mail system.
- **NotifyPayrollTeam** notifies payroll admins when a payroll run completes.

---

## Queue Architecture

Heavy operations are dispatched to the queue for asynchronous execution:

| Job | Purpose | Retries / Backoff |
|-----|---------|-------------------|
| `ProcessPayroll` | Full payroll run with payslip creation | 3 tries; [60s, 300s] |
| `GeneratePayslips` | Batch PDF payslip compilation | 3 tries; [60s, 300s] |
| `SendNotificationEmails` | Bulk email dispatch | 3 tries |
| `GenerateReport` | Background report generation | 3 tries |

The application uses the database queue driver by default (`QUEUE_CONNECTION=database`). A scheduled command in `bootstrap/app.php` retries failed jobs hourly:

```php
$schedule->command('queue:retry --failed')->hourly();
```

A `queue:monitor` Artisan command reports queue health (pending, failed, scheduled, active sessions) and can retry failed jobs:

```bash
php artisan queue:monitor
php artisan queue:monitor --retry-failed
```

---

## Caching Strategy

- **Repository Cache**: `CachedEmployeeRepository` and `CachedPayrollRepository` wrap read operations with `Cache::remember` using version-namespace invalidation.
- **Default TTL**: 3600 seconds (1 hour); search queries use a 30-minute TTL.
- **Cache Invalidation**: Bumping the namespace version on any write operation.
- **Production**: Configure `CACHE_STORE=redis` for distributed, fast cache.

---

## Authorization

Authorization uses a hybrid approach:

1. **Role & Permission Middleware**: `RoleMiddleware` and `PermissionMiddleware` check session-cached role/permission arrays on routes.
2. **Laravel Policies**: `EmployeePolicy`, `LeavePolicy`, `PayrollPolicy`, `DepartmentPolicy` registered via `Gate::policy()`.
3. **Super Admin Bypass**: A `Gate::before` hook grants all abilities to `super_admin` role users.

---

## Query Optimization

- **Eager Loading**: All index controllers explicitly `with()` relationships (`Employee::with(['user', 'department'])`, `LeaveRequest::with(['employee.user', 'approver'])`, etc.) to eliminate N+1 queries.
- **Lazy Loading Prevention**: `Model::preventLazyLoading()` is enabled outside production, so any accidental lazy load throws an exception during development/testing.
- **Silent Discard Prevention**: `preventSilentlyDiscardingAttributes()` is enabled outside production to catch mass-assignment mistakes.
- **Load Testing**: `php artisan app:load-test` benchmarks dashboard aggregations, list queries, reports, and payroll calculations.

---

## Testing Strategy

- **Feature Tests** (`tests/Feature`): HTTP-level tests for auth, employees, payroll, attendance, leave, recruitment, performance, benefits, notifications, reports, shifts, COBRA, and security. 134 tests / 260 assertions.
- **Unit Tests** (`tests/Unit`): Service layer tests for `TaxEngine`, `PayrollCalculator`, `AttendanceProcessor`, `LeaveCalculator`, `EmployeeService`, `CustomAuthService`, `TokenService`, `OrgChartService`, benefit services, and repository tests.
- **Configuration**: SQLite in-memory via `phpunit.xml`; `RefreshDatabase` trait for migrations.

Run the suite:

```bash
php artisan test
```

---

## Related Documents

- [Deployment Guide](deployment.md)
- [Root README](../README.md)
- [Task Backlog](../tasks/README.md)