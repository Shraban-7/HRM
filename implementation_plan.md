# Implementation Plan

[Overview]
Complete the HRM system by building all missing layers (Services, Repositories, Events/Listeners, Jobs, Providers, configs, views, JS components, and tests) on top of the existing Laravel foundation that already has Models, Controllers, Middleware, Seeders, Routes, and core layout views.

The codebase already has a solid foundation with all 15+ Eloquent Models, 26 Controllers across 8 modules, 4 Middleware (CustomAuth, Role, Permission, AuditLog), core Auth services (CustomAuthService, PasswordService, TokenService), HashHelper, complete Seeders (Roles, Permissions, Departments, Admin User), fully wired routes in web.php, middleware registered in bootstrap/app.php, and base layout views with Tailwind v4 + Vite 8 + dompdf. What remains is the middle layer: business logic Services, Repository pattern with Contracts/Eloquent/Cache implementations, Event/Listener wiring, Queue Jobs for async processing, additional Providers, the permissions config file, missing Blade views, JS components/pages, Form Requests, Factories, and a comprehensive test suite.

Key approach:
- Keep domain logic in Services and persistence in Repositories (Eloquent adapters), using Controllers only for orchestration/validation — matching the existing controller pattern.
- Implement Repository contracts + Eloquent implementations with optional Cache decorators using Redis tags.
- Wire all Events to their Listeners and register in EventServiceProvider.
- Use queue Jobs (database driver) for bulk/expensive tasks (payroll processing, payslip generation, notifications, reports).
- Add missing Blade views following the existing app.blade.php layout pattern with Tailwind utility classes.
- Add JS components and page scripts following the jQuery-based pattern outlined in implementation.md.
- Add Form Requests for validation separation in CRUD-heavy controllers.
- Add Database Factories for all models to support testing.
- Write Feature and Unit tests.

[Types]
Consolidate/provide the existing data contracts (Laravel model fields / request validation rules) that already exist and document any additions needed.

The database schema is already fully defined in:
- `database/migrations/0001_01_01_000000_create_users_table.php` (users + sessions tables)
- `database/migrations/0001_01_01_000003_create_hr_migrations.php` (all 19 HRM tables in single migration)

Existing table structure (verified against migrations):

1. **users**: id, email (unique), username (unique), password_hash, salt, status (default 'active'), require_2fa (default false), remember_token, timestamps
2. **sessions**: id (PK string), user_id (FK->users), ip_address, user_agent, payload (longtext), last_activity (int)
3. **password_resets**: id, user_id (FK->users), token_hash, expires_at (timestamp), used_at (nullable), timestamps
4. **roles**: id, name, slug (unique), description (nullable), timestamps
5. **permissions**: id, name, slug (unique), module, timestamps
6. **role_permission**: role_id (FK->roles), permission_id (FK->permissions), composite PK
7. **user_role**: user_id (FK->users), role_id (FK->roles), composite PK
8. **departments**: id, name, parent_id (nullable FK->departments), manager_id (nullable FK->users), timestamps
9. **employees**: id, user_id (unique FK->users), department_id (FK->departments), employee_id (unique), hire_date (date), designation, salary (decimal 12,2), status (default 'active'), profile_photo_path (nullable), timestamps
10. **payrolls**: id, period_start (date), period_end (date), status (default 'draft'), total_amount (decimal 12,2 default 0), timestamps
11. **payslips**: id, employee_id (FK->employees), payroll_id (FK->payrolls), gross (12,2), deductions (12,2), net (12,2), issued_at (nullable), pdf_path (nullable), timestamps
12. **attendance**: employee_id (FK), date, clock_in (nullable), clock_out (nullable), status (default 'present'), composite PK(employee_id, date)
13. **leave_requests**: id, employee_id (FK), type (string), start_date, end_date, status (default 'pending'), approved_by (nullable FK->users), approved_at (nullable), timestamps
14. **job_posts**: id, title, description (text), requirements (nullable text), status (default 'draft'), closing_date (nullable), timestamps
15. **applicants**: id, job_post_id (FK), name, email, phone (nullable), resume_path (nullable), status (default 'applied'), timestamps
16. **interviews**: id, applicant_id (FK), job_post_id (FK), scheduled_at (datetime), status (default 'scheduled'), notes (nullable text), timestamps
17. **performance_reviews**: id, employee_id (FK), reviewer_id (FK->users), review_date (date), rating (nullable int), notes (nullable text), status (default 'pending'), timestamps
18. **goals**: id, employee_id (FK), title, description (nullable text), target_date (nullable), status (default 'pending'), timestamps
19. **feedback**: id, from_user_id (FK->users), to_user_id (FK->users), message (text), category (default 'general'), timestamps
20. **benefits**: id, name, description (nullable text), cost (12,2 default 0), type (default 'health'), is_active (default true), timestamps
21. **benefit_enrollments**: id, employee_id (FK), benefit_id (FK), enrolled_at (date), expires_at (nullable), status (default 'active'), timestamps
22. **audit_logs**: id, user_id (nullable FK->users), action (string), resource (string), resource_id (nullable string), before_data (nullable longtext), after_data (nullable longtext), ip_address (nullable string 45), created_at (useCurrent)

Authorization types (already used by middleware):
- Permission string format: `{module}.{action}` (e.g., `payroll.run_payroll`, `employees.view`)
- Role slug format: lowercase snake-case (e.g., `hr_admin`, `super_admin`, `manager`, `employee`, `payroll_admin`, `recruiter`)
- All 24 permissions defined in `database/seeders/PermissionSeeder.php`
- All 6 roles defined in `database/seeders/RoleSeeder.php`

New Form Request types needed:
- `app/Http/Requests/Employee/StoreEmployeeRequest.php` - validation rules for employee creation
- `app/Http/Requests/Employee/UpdateEmployeeRequest.php` - validation rules for employee update
- `app/Http/Requests/Payroll/StorePayrollRequest.php` - validation for payroll creation
- `app/Http/Requests/Attendance/ClockInRequest.php` - clock-in validation
- `app/Http/Requests/Attendance/LeaveRequest.php` - leave request validation rules

[Files]
Create all missing middle-layer files (Services, Repositories, Events, Listeners, Jobs, Providers, config, views, JS, Form Requests, Factories, Tests) to complete the architecture.

**EXISTING FILES (no changes needed):**
All Models (15+), all Controllers (26), all Middleware (4), CustomAuthService, PasswordService, TokenService, OrgChartService, HashHelper, EncryptionHelper, all Seeders (5), routes/web.php, bootstrap/app.php, config/hrm.php, layouts/app.blade.php, layouts/guest.blade.php, components/guest-layout.blade.php, layout partials (sidebar, header, footer, scripts), auth/* views (4), dashboard/hr.blade.php, employee/index.blade.php, employee/create.blade.php, payroll/index.blade.php, welcome.blade.php, resources/js/app.js (stub), resources/css/app.css (stub), composer.json, package.json, vite.config.js

**NEW FILES TO CREATE:**

### Services (7 files)
- `app/Services/Employee/EmployeeService.php` - CRUD business logic for employees + user account creation
- `app/Services/Payroll/PayrollCalculator.php` - Salary calculation engine with configurable tax/deduction rules
- `app/Services/Payroll/TaxEngine.php` - Tax bracket calculations using config/hrm.php brackets
- `app/Services/Payroll/PayslipGenerator.php` - PDF payslip generation using dompdf
- `app/Services/Attendance/AttendanceProcessor.php` - Clock in/out processing, status determination, overtime calc
- `app/Services/Attendance/LeaveCalculator.php` - Leave balance tracking, accrual, entitlement calculations
- `app/Services/Notification/EmailNotificationService.php` - Email sending (Mail facade)
- `app/Services/Notification/InAppNotificationService.php` - In-app notification storage (stub/basic)

### Repositories (9 files)
- `app/Repositories/Contracts/EmployeeRepositoryInterface.php` - Interface for employee data access
- `app/Repositories/Contracts/PayrollRepositoryInterface.php` - Interface for payroll data access
- `app/Repositories/Contracts/AttendanceRepositoryInterface.php` - Interface for attendance data access
- `app/Repositories/Eloquent/EmployeeRepository.php` - Eloquent implementation
- `app/Repositories/Eloquent/PayrollRepository.php` - Eloquent implementation
- `app/Repositories/Eloquent/AttendanceRepository.php` - Eloquent implementation
- `app/Repositories/Cache/CachedEmployeeRepository.php` - Redis cache decorator wrapping EloquentEmployeeRepo
- `app/Repositories/Cache/CachedPayrollRepository.php` - Redis cache decorator wrapping EloquentPayrollRepo

### Events (6 files)
- `app/Events/EmployeeHired.php` - Fired when employee created
- `app/Events/EmployeeTerminated.php` - Fired when employee terminated
- `app/Events/SalaryChanged.php` - Fired when salary updated
- `app/Events/PayrollProcessed.php` - Fired when payroll run completes
- `app/Events/LeaveApproved.php` - Fired when leave request approved
- `app/Events/PerformanceReviewSubmitted.php` - Fired when review submitted

### Listeners (4 files)
- `app/Listeners/SendWelcomeEmail.php` - Sends welcome email on EmployeeHired
- `app/Listeners/UpdateOrgChart.php` - Updates org chart cache on EmployeeHired/EmployeeTerminated
- `app/Listeners/NotifyPayrollTeam.php` - Notifies team on PayrollProcessed
- `app/Listeners/LogAuditEvent.php` - Logs audit entry for events

### Jobs (4 files)
- `app/Jobs/ProcessPayroll.php` - Queue job for async payroll processing
- `app/Jobs/GeneratePayslips.php` - Queue job for batch payslip PDF generation
- `app/Jobs/SendNotificationEmails.php` - Queue job for bulk email sending
- `app/Jobs/GenerateReport.php` - Queue job for background report generation

### Providers (3 files)
- `app/Providers/AuthServiceProvider.php` - Register auth gates/policies (currently empty)
- `app/Providers/EventServiceProvider.php` - Map events to listeners
- `app/Providers/RepositoryServiceProvider.php` - Bind repository interfaces to implementations

### Config (1 file)
- `config/permissions.php` - Permission strings and role-permission mapping (used as reference/fallback)

### Views - Missing Blade templates

**Employee views (3 files):**
- `resources/views/employee/edit.blade.php` - Edit employee form
- `resources/views/employee/show.blade.php` - Employee detail view with tabs (info, payslips, attendance, leave)
- `resources/views/employee/org-chart.blade.php` - Organization chart tree view

**Dashboard views (2 files):**
- `resources/views/dashboard/employee.blade.php` - Employee dashboard (my attendance, leave balance, payslips)
- `resources/views/dashboard/manager.blade.php` - Manager dashboard (team overview, pending approvals)

**Payroll views (3 files):**
- `resources/views/payroll/calculate.blade.php` - Payroll calculation form / results
- `resources/views/payroll/payslips.blade.php` - Payslip listing for a payroll
- `resources/views/payroll/show.blade.php` - Payroll detail with employee payslips

**Attendance views (4 files):**
- `resources/views/attendance/index.blade.php` - Attendance listing with filters
- `resources/views/attendance/clock-in.blade.php` - Clock in/out button UI
- `resources/views/attendance/leave-requests.blade.php` - Leave requests list + form
- `resources/views/attendance/calendar.blade.php` - Monthly attendance calendar

**Recruitment views (3 files):**
- `resources/views/recruitment/jobs.blade.php` - Job posts list + create
- `resources/views/recruitment/applicants.blade.php` - Applicants pipeline
- `resources/views/recruitment/interviews.blade.php` - Interviews schedule

**Performance views (3 files):**
- `resources/views/performance/reviews.blade.php` - Reviews list + create
- `resources/views/performance/goals.blade.php` - Goals list + create
- `resources/views/performance/feedback.blade.php` - Feedback list + form

**Benefits views (2 files):**
- `resources/views/benefits/index.blade.php` - Benefit catalog
- `resources/views/benefits/enroll.blade.php` - Enrollment form

**Audit view (1 file):**
- `resources/views/layouts/audit.blade.php` - Audit log viewer (already referenced in routes)

**Additional layout (1 file for the route reference):**
- `resources/views/layouts/partials/head.blade.php` - Head partial (meta, title, styles) referenced by implementation.md pattern

### JavaScript (7 files)
- `resources/js/components/modal.js` - Modal component (jQuery)
- `resources/js/components/datatable.js` - DataTable component (jQuery DataTables wrapper)
- `resources/js/components/chart.js` - Chart.js wrapper component
- `resources/js/components/notification.js` - Toast notification component
- `resources/js/pages/employee.js` - Employee page scripts (DataTable init, delete confirmation)
- `resources/js/pages/payroll.js` - Payroll page scripts
- `resources/js/pages/attendance.js` - Attendance page scripts

### Form Requests (5 files)
- `app/Http/Requests/Employee/StoreEmployeeRequest.php`
- `app/Http/Requests/Employee/UpdateEmployeeRequest.php`
- `app/Http/Requests/Payroll/StorePayrollRequest.php`
- `app/Http/Requests/Attendance/ClockInRequest.php`
- `app/Http/Requests/Attendance/LeaveRequest.php`

### Factories (10+ files)
- `database/factories/UserFactory.php` (exists, update)
- `database/factories/EmployeeFactory.php`
- `database/factories/DepartmentFactory.php`
- `database/factories/PayrollFactory.php`
- `database/factories/PayslipFactory.php`
- `database/factories/AttendanceFactory.php`
- `database/factories/LeaveRequestFactory.php`
- `database/factories/JobPostFactory.php`
- `database/factories/ApplicantFactory.php`
- `database/factories/PerformanceReviewFactory.php`
- `database/factories/BenefitFactory.php`
- `database/factories/BenefitEnrollmentFactory.php`
- `database/factories/RoleFactory.php`
- `database/factories/PermissionFactory.php`

### Tests (6 files)
- `tests/Feature/AuthTest.php` - Login, registration, password reset, middleware blocking
- `tests/Feature/EmployeeTest.php` - CRUD, permissions, search
- `tests/Feature/PayrollTest.php` - Payroll calculation, payslip generation
- `tests/Feature/AttendanceTest.php` - Clock in/out, leave request workflow
- `tests/Unit/Services/PayrollCalculatorTest.php` - Tax bracket math, deduction rules
- `tests/Unit/Services/TaxEngineTest.php` - Tax bracket calculation unit tests

**EXISTING FILES TO MODIFY (minor wiring updates):**
- `resources/js/app.js` - Import and initialize all components and page scripts
- `resources/css/app.css` - Add Tailwind directives (if not already using `@tailwind` or `@import "tailwindcss"`)
- `app/Providers/AppServiceProvider.php` - Register repository bindings in register() method
- `config/hrm.php` - Already exists and is complete; no changes needed unless testing reveals gaps

[Functions]
Implement/modify service methods, repository methods, event/listener/job handlers, and validation rules to provide the complete business logic layer.

### New Function Implementations

**`app/Services/Employee/EmployeeService.php`:**
- `createEmployee(array $userData, array $employeeData): Employee` - Creates User account + Employee record, fires EmployeeHired event
- `updateEmployee(Employee $employee, array $data): Employee` - Updates employee, fires SalaryChanged if salary changes
- `terminateEmployee(Employee $employee): void` - Sets status to terminated, fires EmployeeTerminated
- `getEmployeeWithDetails(Employee $employee): Employee` - Loads all relationships

**`app/Services/Payroll/PayrollCalculator.php`:**
- `calculatePayroll(int $payrollId): array` - Iterates employees, calculates gross/deductions/net
- `calculateEmployeePay(Employee $employee, Carbon $periodStart, Carbon $periodEnd): array` - Per-employee calculation
- `applyDeductions(float $gross, Employee $employee): float` - Applies configurable deductions
- `processPayroll(int $payrollId): Payroll` - Orchestrates full payroll run, creates/updates payslips

**`app/Services/Payroll/TaxEngine.php`:**
- `calculateTax(float $gross, array $brackets = []): float` - Progressive tax calculation
- `getEffectiveTaxRate(float $gross): float` - Returns effective tax rate for a gross amount

**`app/Services/Payroll/PayslipGenerator.php`:**
- `generatePayslip(Payslip $payslip): string` - Generates PDF using dompdf, returns file path
- `generateBatchPayslip(int $payrollId): array` - Generates PDFs for all payslips in a payroll

**`app/Services/Attendance/AttendanceProcessor.php`:**
- `clockIn(Employee $employee): Attendance` - Creates attendance record
- `clockOut(Employee $employee): Attendance` - Updates clock_out, determines status
- `getAttendanceForPeriod(Employee $employee, $start, $end): Collection`
- `calculateOvertime(Employee $employee, Carbon $date): float`

**`app/Services/Attendance/LeaveCalculator.php`:**
- `getLeaveBalance(Employee $employee, string $type): int` - Returns remaining days
- `calculateAccrual(Employee $employee, string $type): float`
- `isEligibleForLeave(Employee $employee, string $type, int $days): bool`

**`app/Services/Notification/EmailNotificationService.php`:**
- `sendWelcomeEmail(User $user): void`
- `sendPasswordResetEmail(User $user, string $token): void`
- `sendLeaveApprovalEmail(LeaveRequest $leave): void`
- `sendPayrollNotification(Payroll $payroll): void`

**`app/Services/Notification/InAppNotificationService.php`:**
- `notify(User $user, string $message, string $type): void` - Stores notification in DB (basic implementation)

### Repository Interface Methods

**`EmployeeRepositoryInterface`:**
- `findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator`
- `findById(int $id): ?Employee`
- `findByUserId(int $userId): ?Employee`
- `findByDepartment(int $departmentId): Collection`
- `search(string $query): Collection`
- `create(array $data): Employee`
- `update(Employee $employee, array $data): Employee`
- `delete(Employee $employee): void`

**`PayrollRepositoryInterface`:**
- `findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator`
- `findById(int $id): ?Payroll`
- `create(array $data): Payroll`
- `update(Payroll $payroll, array $data): Payroll`
- `getPayslipsForPayroll(int $payrollId): Collection`

**`AttendanceRepositoryInterface`:**
- `findForEmployee(int $employeeId, array $filters = []): Collection`
- `findByDate(int $employeeId, Carbon $date): ?Attendance`
- `create(array $data): Attendance`
- `update(Attendance $attendance, array $data): Attendance`
- `getMonthlyAttendance(int $employeeId, int $year, int $month): Collection`

### Event/Listener Handlers
- `EmployeeHired` -> `SendWelcomeEmail::class`, `UpdateOrgChart::class`, `LogAuditEvent::class`
- `EmployeeTerminated` -> `LogAuditEvent::class`, `UpdateOrgChart::class`
- `SalaryChanged` -> `LogAuditEvent::class`
- `PayrollProcessed` -> `NotifyPayrollTeam::class`, `LogAuditEvent::class`
- `LeaveApproved` -> `LogAuditEvent::class`
- `PerformanceReviewSubmitted` -> `LogAuditEvent::class`

### Job Handlers
- `ProcessPayroll::handle()` -> Calls PayrollCalculator, creates Payslips, dispatches PayslipGenerated events
- `GeneratePayslips::handle()` -> Calls PayslipGenerator for batch processing
- `SendNotificationEmails::handle()` -> Sends queued notification emails
- `GenerateReport::handle()` -> Generates report in background, stores result

[Classes]
Add/modify the middle-layer classes (Services, Repositories, Events, Listeners, Jobs, Providers, Form Requests) to complete the architecture.

### New Classes

**Services:**
- `EmployeeService` - `app/Services/Employee/EmployeeService.php` - Orchestrates employee operations with events
- `PayrollCalculator` - `app/Services/Payroll/PayrollCalculator.php` - Payroll calculation engine
- `TaxEngine` - `app/Services/Payroll/TaxEngine.php` - Tax bracket progression calculator
- `PayslipGenerator` - `app/Services/Payroll/PayslipGenerator.php` - PDF generation via dompdf
- `AttendanceProcessor` - `app/Services/Attendance/AttendanceProcessor.php` - Clock in/out logic
- `LeaveCalculator` - `app/Services/Attendance/LeaveCalculator.php` - Leave balance management
- `EmailNotificationService` - `app/Services/Notification/EmailNotificationService.php`
- `InAppNotificationService` - `app/Services/Notification/InAppNotificationService.php`

**Repositories:**
- Interfaces: `EmployeeRepositoryInterface`, `PayrollRepositoryInterface`, `AttendanceRepositoryInterface`
- Eloquent: `EmployeeRepository`, `PayrollRepository`, `AttendanceRepository`
- Cache: `CachedEmployeeRepository`, `CachedPayrollRepository`

**Events** (all extend `Illuminate\Foundation\Events\Dispatchable`):
- `EmployeeHired` - `app/Events/EmployeeHired.php` - Contains `$employee` public property
- `EmployeeTerminated` - `app/Events/EmployeeTerminated.php` - Contains `$employee` public property
- `SalaryChanged` - `app/Events/SalaryChanged.php` - Contains `$employee`, `$oldSalary`, `$newSalary`
- `PayrollProcessed` - `app/Events/PayrollProcessed.php` - Contains `$payroll` public property
- `LeaveApproved` - `app/Events/LeaveApproved.php` - Contains `$leaveRequest` public property
- `PerformanceReviewSubmitted` - `app/Events/PerformanceReviewSubmitted.php` - Contains `$review` public property

**Listeners** (all implement `ShouldQueue`):
- `SendWelcomeEmail` - `app/Listeners/SendWelcomeEmail.php`
- `UpdateOrgChart` - `app/Listeners/UpdateOrgChart.php`
- `NotifyPayrollTeam` - `app/Listeners/NotifyPayrollTeam.php`
- `LogAuditEvent` - `app/Listeners/LogAuditEvent.php`

**Jobs** (all implement `ShouldQueue`):
- `ProcessPayroll` - `app/Jobs/ProcessPayroll.php`
- `GeneratePayslips` - `app/Jobs/GeneratePayslips.php`
- `SendNotificationEmails` - `app/Jobs/SendNotificationEmails.php`
- `GenerateReport` - `app/Jobs/GenerateReport.php`

### Existing Classes to Modify

- `AppServiceProvider` - Add repository bindings and service registrations
- `resources/js/app.js` - Add imports for JS components and page scripts

### Event-Listener Mappings (in EventServiceProvider)
```php
protected $listen = [
    EmployeeHired::class => [SendWelcomeEmail::class, UpdateOrgChart::class, LogAuditEvent::class],
    EmployeeTerminated::class => [LogAuditEvent::class, UpdateOrgChart::class],
    SalaryChanged::class => [LogAuditEvent::class],
    PayrollProcessed::class => [NotifyPayrollTeam::class, LogAuditEvent::class],
    LeaveApproved::class => [LogAuditEvent::class],
    PerformanceReviewSubmitted::class => [LogAuditEvent::class],
];
```

[Dependencies]
No new packages required. All necessary dependencies are already in composer.json and package.json.

**Existing dependencies (already installed):**
- `dompdf/dompdf: ^3.1` - PDF generation for payslips
- `laravel/framework: ^13.8` - Laravel framework
- `laravel/tinker: ^3.0` - REPL
- `@tailwindcss/vite: ^4.0` - Tailwind CSS v4 Vite plugin
- `tailwindcss: ^4.0` - Utility-first CSS framework
- `vite: ^8.0` - Build tool
- `laravel-vite-plugin: ^3.1` - Laravel Vite integration

**No new packages needed.** The application uses:
- Database sessions (already configured in .env.example: `SESSION_DRIVER=database`)
- Database queue (already configured: `QUEUE_CONNECTION=database`)
- Database cache (already configured: `CACHE_STORE=database`)
- Redis support available if needed (REDIS_HOST/REDIS_PORT in .env.example)
- Mail via log driver for development

[Testing]
Create feature tests for the main workflows and unit tests for service layer calculations.

**Test files to create:**

1. `tests/Feature/AuthTest.php`:
   - `test_user_can_view_login_page()`
   - `test_user_can_login_with_valid_credentials()`
   - `test_user_cannot_login_with_invalid_credentials()`
   - `test_user_can_register()`
   - `test_authenticated_user_can_access_dashboard()`
   - `test_unauthenticated_user_is_redirected_to_login()`
   - `test_middleware_blocks_routes_without_permission()`

2. `tests/Feature/EmployeeTest.php`:
   - `test_can_list_employees()`
   - `test_can_create_employee()`
   - `test_can_view_employee()`
   - `test_can_update_employee()`
   - `test_can_terminate_employee()`

3. `tests/Feature/PayrollTest.php`:
   - `test_can_view_payroll_index()`
   - `test_can_create_payroll()`
   - `test_payroll_calculation_returns_payslips()`

4. `tests/Feature/AttendanceTest.php`:
   - `test_can_clock_in()`
   - `test_can_clock_out()`
   - `test_can_submit_leave_request()`
   - `test_can_approve_leave_request()`

5. `tests/Unit/Services/PayrollCalculatorTest.php`:
   - `test_calculate_employee_pay_returns_correct_gross()`
   - `test_apply_deductions_reduces_net_pay()`

6. `tests/Unit/Services/TaxEngineTest.php`:
   - `test_calculate_tax_within_bracket_returns_correct_amount()`
   - `test_calculate_tax_across_brackets_returns_cumulative_amount()`
   - `test_get_effective_tax_rate_returns_percentage()`

**Validation strategy:**
- Use SQLite in-memory database for unit tests
- Use database transactions for feature tests (RefreshDatabase trait)
- Seed roles and permissions before feature tests that check middleware
- Mock Mail facade for email notification tests

**Existing factory to update:**
- `database/factories/UserFactory.php` - Update to generate salt + password_hash using HashHelper

[Implementation Order]
Build the missing layers top-down from Services (core business logic) down to Tests (validation), creating views and JS alongside their module counterpart.

Implementation sequence (15 steps):

1. **Create Config** - Create `config/permissions.php` with module permission arrays and role-permission mappings (as reference config)
2. **Create JS Components** - Build `resources/js/app.js` imports, components (modal, datatable, chart, notification), and page scripts (employee, payroll, attendance)
3. **Create Services** - Build all missing Services (EmployeeService, PayrollCalculator, TaxEngine, PayslipGenerator, AttendanceProcessor, LeaveCalculator, EmailNotificationService, InAppNotificationService)
4. **Create Repository Interfaces** - Build contract interfaces (EmployeeRepositoryInterface, PayrollRepositoryInterface, AttendanceRepositoryInterface)
5. **Create Eloquent Repositories** - Build Eloquent implementations for all three repository interfaces
6. **Create Cache Repositories** - Build Cached decorators wrapping Eloquent implementations
7. **Create Events** - Build all 6 Event classes (EmployeeHired, EmployeeTerminated, SalaryChanged, PayrollProcessed, LeaveApproved, PerformanceReviewSubmitted)
8. **Create Listeners** - Build all 4 Listener classes (SendWelcomeEmail, UpdateOrgChart, NotifyPayrollTeam, LogAuditEvent)
9. **Create Jobs** - Build all 4 Job classes (ProcessPayroll, GeneratePayslips, SendNotificationEmails, GenerateReport)
10. **Create Providers** - Build EventServiceProvider, RepositoryServiceProvider, update AppServiceProvider with bindings
11. **Create Form Requests** - Build 5 Form Request classes for employee/payroll/attendance validation
12. **Create Views** - Build all missing Blade views (employee/*, dashboard/*, payroll/*, attendance/*, recruitment/*, performance/*, benefits/*, audit, head partial)
13. **Create Factories** - Build Database Factories for all models, update UserFactory
14. **Create Tests** - Build Feature tests (Auth, Employee, Payroll, Attendance) and Unit tests (PayrollCalculator, TaxEngine)
15. **Final Wiring** - Verify all routes resolve, middleware works, and compile assets with Vite