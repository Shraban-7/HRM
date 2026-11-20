# Modules and Workflows

The modules below are reflected in the current route, controller, service, and model structure. Exact role permissions and workflow details are enforced by the current routes, middleware, policies, and services; inspect those implementations before changing behavior.

| Area | Main code locations | Purpose |
|---|---|---|
| Authentication and access | `app/Services/Auth/`, `app/Middleware/`, `app/Policies/`, `app/Models/Role.php` | Login, password reset, session-backed user context, roles, and permissions. |
| Employees and organization | `app/Http/Controllers/Employee/`, `app/Services/Employee/`, `app/Models/Employee.php` | Employee lifecycle data, departments, and organization chart. |
| Payroll | `app/Services/Payroll/`, `app/Jobs/`, `app/Http/Controllers/Payroll/` | Payroll calculation, tax calculations, processing jobs, and payslips. |
| Time and leave | `app/Services/Attendance/`, `app/Http/Controllers/Attendance/` | Clock records, shifts, leave requests, approvals, and balance calculations. |
| Recruitment | `app/Http/Controllers/Recruitment/`, `app/Models/Applicant.php` | Public job listings and application intake, plus internal applicant and interview management. |
| Performance | `app/Http/Controllers/Performance/`, `app/Services/Performance/` | Goals, feedback, review cycles, form templates, calibration, and bonus calculations. |
| Benefits | `app/Services/Benefits/`, `app/Http/Controllers/Benefits/` | Benefit catalog, enrollment, cost calculations, deductions, and COBRA coverage. |
| Notifications | `app/Notifications/`, `app/Services/Notification/`, `app/Listeners/` | In-app notification records and email delivery. |
| Reports and audit | `app/Http/Controllers/Reports/`, `app/Middleware/AuditLogMiddleware.php`, `app/Listeners/LogAuditEvent.php` | Report generation and request/domain audit records. |

## Typical workflow boundaries

- Public routes include authentication screens and the careers portal. Most HR operations are behind `custom.auth` in `routes/web.php`.
- A controller accepts a request and selects a response; validation belongs in Form Requests or Laravel validation, while domain calculations belong in services.
- Long-running work can be represented by jobs. Events and listeners handle side effects such as notifications and audit records.
- Database schema is maintained through ordered migrations in `database/migrations/`. Model relationships and constraints should stay aligned with the schema.
- Authorization is not uniform across every route. Review its middleware and policy before adding or exposing actions.

## Extending a module

Before adding a workflow, trace its route through controller, validation, service, model/repository, and view. Identify who may initiate and approve each state transition. Keep money calculations deterministic and testable, persist related state changes transactionally, and dispatch external side effects only after durable state is saved.
