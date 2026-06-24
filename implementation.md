# HRM Implementation Plan

## Tech Stack
- **Backend**: Laravel 11.x
- **Frontend CSS**: Tailwind CSS 4.x
- **Template Engine**: Blade (native Laravel)
- **JavaScript**: jQuery 3.7.x
- **Database**: MySQL 8.0 / PostgreSQL 15
- **Cache/Queue**: Redis 7.x
- **Auth**: Custom implementation (no packages)

---

## 1. Project Structure

```
hrm-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── RegisterController.php
│   │   │   │   ├── ForgotPasswordController.php
│   │   │   │   ├── ResetPasswordController.php
│   │   │   │   └── LogoutController.php
│   │   │   ├── Dashboard/
│   │   │   │   ├── EmployeeDashboardController.php
│   │   │   │   ├── ManagerDashboardController.php
│   │   │   │   └── HRDashboardController.php
│   │   │   ├── Employee/
│   │   │   │   ├── EmployeeController.php
│   │   │   │   ├── DepartmentController.php
│   │   │   │   └── OrgChartController.php
│   │   │   ├── Payroll/
│   │   │   │   ├── PayrollController.php
│   │   │   │   ├── PayslipController.php
│   │   │   │   └── TaxController.php
│   │   │   ├── Attendance/
│   │   │   │   ├── AttendanceController.php
│   │   │   │   ├── LeaveController.php
│   │   │   │   └── ShiftController.php
│   │   │   ├── Recruitment/
│   │   │   │   ├── JobPostController.php
│   │   │   │   ├── ApplicantController.php
│   │   │   │   └── InterviewController.php
│   │   │   ├── Performance/
│   │   │   │   ├── ReviewController.php
│   │   │   │   ├── GoalController.php
│   │   │   │   └── FeedbackController.php
│   │   │   └── Benefits/
│   │   │       ├── BenefitController.php
│   │   │       └── EnrollmentController.php
│   │   ├── Middleware/
│   │   │   ├── CustomAuth.php
│   │   │   ├── RoleMiddleware.php
│   │   │   ├── PermissionMiddleware.php
│   │   │   └── AuditLogMiddleware.php
│   │   └── Requests/
│   │       ├── Auth/
│   │       ├── Employee/
│   │       ├── Payroll/
│   │       └── Attendance/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Employee.php
│   │   ├── Department.php
│   │   ├── Payroll.php
│   │   ├── Payslip.php
│   │   ├── Attendance.php
│   │   ├── LeaveRequest.php
│   │   ├── JobPost.php
│   │   ├── Applicant.php
│   │   ├── PerformanceReview.php
│   │   ├── Benefit.php
│   │   ├── BenefitEnrollment.php
│   │   ├── AuditLog.php
│   │   ├── Role.php
│   │   ├── Permission.php
│   │   └── PasswordReset.php
│   ├── Repositories/
│   │   ├── Contracts/
│   │   │   ├── EmployeeRepositoryInterface.php
│   │   │   ├── PayrollRepositoryInterface.php
│   │   │   └── AttendanceRepositoryInterface.php
│   │   ├── Eloquent/
│   │   │   ├── EmployeeRepository.php
│   │   │   ├── PayrollRepository.php
│   │   │   └── AttendanceRepository.php
│   │   └── Cache/
│   │       ├── CachedEmployeeRepository.php
│   │       └── CachedPayrollRepository.php
│   ├── Services/
│   │   ├── Auth/
│   │   │   ├── CustomAuthService.php
│   │   │   ├── PasswordService.php
│   │   │   └── TokenService.php
│   │   ├── Employee/
│   │   │   ├── EmployeeService.php
│   │   │   └── OrgChartService.php
│   │   ├── Payroll/
│   │   │   ├── PayrollCalculator.php
│   │   │   ├── TaxEngine.php
│   │   │   └── PayslipGenerator.php
│   │   ├── Attendance/
│   │   │   ├── AttendanceProcessor.php
│   │   │   └── LeaveCalculator.php
│   │   └── Notification/
│   │       ├── EmailNotificationService.php
│   │       └── InAppNotificationService.php
│   ├── Events/
│   │   ├── EmployeeHired.php
│   │   ├── EmployeeTerminated.php
│   │   ├── SalaryChanged.php
│   │   ├── PayrollProcessed.php
│   │   ├── LeaveApproved.php
│   │   └── PerformanceReviewSubmitted.php
│   ├── Listeners/
│   │   ├── SendWelcomeEmail.php
│   │   ├── UpdateOrgChart.php
│   │   ├── NotifyPayrollTeam.php
│   │   └── LogAuditEvent.php
│   ├── Jobs/
│   │   ├── ProcessPayroll.php
│   │   ├── GeneratePayslips.php
│   │   ├── SendNotificationEmails.php
│   │   └── GenerateReport.php
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   └── RepositoryServiceProvider.php
│   └── Helpers/
│       ├── EncryptionHelper.php
│       └── HashHelper.php
├── bootstrap/
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── queue.php
│   ├── cache.php
│   ├── mail.php
│   ├── hrm.php
│   └── permissions.php
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_users_table.php
│   │   ├── 2024_01_01_000002_create_employees_table.php
│   │   ├── 2024_01_01_000003_create_departments_table.php
│   │   ├── 2024_01_01_000004_create_payrolls_table.php
│   │   ├── 2024_01_01_000005_create_payslips_table.php
│   │   ├── 2024_01_01_000006_create_attendances_table.php
│   │   ├── 2024_01_01_000007_create_leave_requests_table.php
│   │   ├── 2024_01_01_000008_create_job_posts_table.php
│   │   ├── 2024_01_01_000009_create_applicants_table.php
│   │   ├── 2024_01_01_000010_create_performance_reviews_table.php
│   │   ├── 2024_01_01_000011_create_benefits_table.php
│   │   ├── 2024_01_01_000012_create_benefit_enrollments_table.php
│   │   ├── 2024_01_01_000013_create_roles_table.php
│   │   ├── 2024_01_01_000014_create_permissions_table.php
│   │   ├── 2024_01_01_000015_create_role_permission_table.php
│   │   ├── 2024_01_01_000016_create_user_role_table.php
│   │   ├── 2024_01_01_000017_create_audit_logs_table.php
│   │   ├── 2024_01_01_000018_create_password_resets_table.php
│   │   └── 2024_01_01_000019_create_sessions_table.php
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── RoleSeeder.php
│   │   ├── PermissionSeeder.php
│   │   ├── DepartmentSeeder.php
│   │   └── AdminUserSeeder.php
│   └── factories/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── auth.blade.php
│   │   │   ├── dashboard.blade.php
│   │   │   ├── employee.blade.php
│   │   │   ├── payroll.blade.php
│   │   │   └── partials/
│   │   │       ├── head.blade.php
│   │   │       ├── sidebar.blade.php
│   │   │       ├── header.blade.php
│   │   │       ├── footer.blade.php
│   │   │       └── scripts.blade.php
│   │   ├── auth/
│   │   │   ├── login.blade.php
│   │   │   ├── register.blade.php
│   │   │   ├── forgot-password.blade.php
│   │   │   └── reset-password.blade.php
│   │   ├── dashboard/
│   │   │   ├── employee.blade.php
│   │   │   ├── manager.blade.php
│   │   │   └── hr.blade.php
│   │   ├── employee/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   ├── show.blade.php
│   │   │   └── org-chart.blade.php
│   │   ├── payroll/
│   │   │   ├── index.blade.php
│   │   │   ├── calculate.blade.php
│   │   │   ├── payslips.blade.php
│   │   │   └── show.blade.php
│   │   ├── attendance/
│   │   │   ├── index.blade.php
│   │   │   ├── clock-in.blade.php
│   │   │   ├── leave-requests.blade.php
│   │   │   └── calendar.blade.php
│   │   ├── recruitment/
│   │   │   ├── jobs.blade.php
│   │   │   ├── applicants.blade.php
│   │   │   └── interviews.blade.php
│   │   ├── performance/
│   │   │   ├── reviews.blade.php
│   │   │   ├── goals.blade.php
│   │   │   └── feedback.blade.php
│   │   └── benefits/
│   │       ├── index.blade.php
│   │       └── enroll.blade.php
│   ├── css/
│   │   └── app.css
│   └── js/
│       ├── app.js
│       ├── components/
│       │   ├── modal.js
│       │   ├── datatable.js
│       │   ├── chart.js
│       │   └── notification.js
│       └── pages/
│           ├── employee.js
│           ├── payroll.js
│           └── attendance.js
├── routes/
│   ├── web.php
│   ├── api.php
│   └── channels.php
├── public/
│   ├── css/
│   ├── js/
│   ├── images/
│   └── uploads/
├── storage/
├── tests/
│   ├── Feature/
│   │   ├── AuthTest.php
│   │   ├── EmployeeTest.php
│   │   └── PayrollTest.php
│   └── Unit/
│       ├── Services/
│       └── Repositories/
├── artisan
├── composer.json
├── package.json
├── tailwind.config.js
├── vite.config.js
└── .env
```

---

## 2. Tech Task Definitions

### Phase 1: Foundation & Auth (Week 1-2)

| Task ID | Task | Description | Deliverable |
|---------|------|-------------|-------------|
| T1.1 | Laravel Setup | Install Laravel 11, configure database, Redis, mail | Working Laravel base |
| T1.2 | Tailwind Integration | Configure Tailwind CSS with Vite, custom color palette | Compiled CSS with HRM theme |
| T1.3 | Database Schema | Create all 19 migration files | Migration files ready to run |
| T1.4 | Custom Auth Service | Implement login, logout, password hashing with salt+pepper | CustomAuthService.php |
| T1.5 | Auth Controllers | Login, Register, Forgot/Reset Password controllers | 5 controller files |
| T1.6 | Auth Middleware | CustomAuth, RoleMiddleware, PermissionMiddleware | 3 middleware files |
| T1.7 | Auth Views | Blade templates for login, register, forgot/reset password | 4 Blade files with Tailwind |
| T1.8 | Session Management | Custom session table, remember me functionality | Session handling working |
| T1.9 | Seeders | Roles, permissions, admin user, departments | Database seeded |
| T1.10 | Auth Testing | Feature tests for all auth flows | Test files with passing tests |

### Phase 2: Employee Module (Week 3-4)

| Task ID | Task | Description | Deliverable |
|---------|------|-------------|-------------|
| T2.1 | Employee Model | Employee model with relationships | Employee.php |
| T2.2 | Repository Pattern | Employee repository interface + Eloquent implementation | 2 repository files |
| T2.3 | Employee Service | Business logic for CRUD operations | EmployeeService.php |
| T2.4 | Employee Controller | CRUD controller with validation | EmployeeController.php |
| T2.5 | Employee Views | Index, create, edit, show Blade templates | 4 Blade files |
| T2.6 | Department Management | Department CRUD with tree structure | DepartmentController + views |
| T2.7 | Org Chart | Visual organization chart display | OrgChartController + view |
| T2.8 | Employee Search | AJAX search with jQuery | Search endpoint + JS |
| T2.9 | File Upload | Profile photo upload handling | Upload service + storage |
| T2.10 | Employee Events | EmployeeHired, EmployeeTerminated events | Event classes + listeners |

### Phase 3: Payroll Module (Week 5-6)

| Task ID | Task | Description | Deliverable |
|---------|------|-------------|-------------|
| T3.1 | Payroll Models | Payroll, Payslip models | 2 model files |
| T3.2 | Payroll Calculator | Salary calculation engine with tax rules | PayrollCalculator.php |
| T3.3 | Tax Engine | Configurable tax brackets and deductions | TaxEngine.php |
| T3.4 | Payslip Generator | PDF generation for payslips | PayslipGenerator.php |
| T3.5 | Payroll Controller | Run payroll, view history, individual payslips | PayrollController.php |
| T3.6 | Payroll Views | Calculate, index, show payslip views | 4 Blade files |
| T3.7 | Payroll Queue Job | Background processing for bulk payroll | ProcessPayroll.php job |
| T3.8 | Payroll Events | PayrollProcessed event | Event + listeners |
| T3.9 | Payroll Reports | Monthly/yearly payroll reports | Report generation service |
| T3.10 | Payroll Audit | Immutable audit trail for salary changes | Audit logging |

### Phase 4: Attendance Module (Week 7-8)

| Task ID | Task | Description | Deliverable |
|---------|------|-------------|-------------|
| T4.1 | Attendance Models | Attendance, LeaveRequest, Shift models | 3 model files |
| T4.2 | Clock In/Out | Real-time attendance tracking | AttendanceController endpoints |
| T4.3 | Leave Management | Leave request workflow (apply -> approve) | LeaveController + views |
| T4.4 | Shift Management | Work shift configuration and assignment | ShiftController |
| T4.5 | Calendar View | Monthly attendance calendar display | Calendar Blade + JS |
| T4.6 | Attendance Reports | Late coming, absenteeism reports | Report views |
| T4.7 | Leave Balance | Accrual and balance tracking | LeaveCalculator.php |
| T4.8 | Attendance Events | LeaveApproved event | Event + listener |
| T4.9 | Overtime Calculation | OT rules and calculation | Overtime service |
| T4.10 | Integration | Attendance data to payroll integration | Service connector |

### Phase 5: Recruitment Module (Week 9-10)

| Task ID | Task | Description | Deliverable |
|---------|------|-------------|-------------|
| T5.1 | Recruitment Models | JobPost, Applicant, Interview models | 3 model files |
| T5.2 | Job Posting | CRUD for job postings with approval | JobPostController |
| T5.3 | Applicant Tracking | Application submission and status tracking | ApplicantController |
| T5.4 | Interview Scheduling | Calendar-based interview scheduling | InterviewController |
| T5.5 | Resume Upload | File upload and storage for resumes | Upload handling |
| T5.6 | Recruitment Views | Jobs list, applicant pipeline, interviews | 3 Blade files |
| T5.7 | Email Notifications | Automated emails to applicants | EmailNotificationService |
| T5.8 | Hiring Workflow | Convert applicant to employee | Integration with Employee module |
| T5.9 | Recruitment Reports | Source tracking, time-to-hire metrics | Dashboard widgets |
| T5.10 | Career Portal | Public-facing job listings page | Public route + view |

### Phase 6: Performance Module (Week 11-12)

| Task ID | Task | Description | Deliverable |
|---------|------|-------------|-------------|
| T6.1 | Performance Models | PerformanceReview, Goal, Feedback models | 3 model files |
| T6.2 | Review Cycle | Configurable review periods and cycles | ReviewController |
| T6.3 | 360 Feedback | Multi-rater feedback collection | FeedbackController |
| T6.4 | Goal Setting | SMART goals with progress tracking | GoalController |
| T6.5 | Performance Forms | Customizable review form templates | Dynamic form builder |
| T6.6 | Performance Views | Reviews, goals, feedback pages | 3 Blade files |
| T6.7 | Performance Events | PerformanceReviewSubmitted event | Event + listener |
| T6.8 | Analytics | Performance trend charts | Chart.js integration |
| T6.9 | Calibration | Performance rating calibration tool | Manager tool |
| T6.10 | Integration | Performance data to payroll (bonuses) | Service connector |

### Phase 7: Benefits Module (Week 13-14)

| Task ID | Task | Description | Deliverable |
|---------|------|-------------|-------------|
| T7.1 | Benefits Models | Benefit, BenefitEnrollment models | 2 model files |
| T7.2 | Benefit Catalog | Configurable benefit types and plans | BenefitController |
| T7.3 | Enrollment Workflow | Open enrollment and life event changes | EnrollmentController |
| T7.4 | Deduction Calculation | Pre/post-tax deduction calculations | Deduction service |
| T7.5 | Benefits Views | Catalog, enrollment, summary pages | 2 Blade files |
| T7.6 | Provider Integration | API connectors for benefit providers | Provider service |
| T7.7 | Enrollment Reports | Participation and cost reports | Report views |
| T7.8 | Benefits Events | Enrollment change events | Event + listener |
| T7.9 | COBRA Management | Compliance tracking for terminated employees | COBRA service |
| T7.10 | Integration | Benefits data to payroll | Service connector |

### Phase 8: Dashboard & Reporting (Week 15-16)

| Task ID | Task | Description | Deliverable |
|---------|------|-------------|-------------|
| T8.1 | Role-Based Dashboards | Employee, Manager, HR admin dashboards | 3 dashboard controllers |
| T8.2 | Dashboard Widgets | KPI cards, charts, recent activity | Reusable widgets |
| T8.3 | DataTables | jQuery DataTables for all list views | Shared JS component |
| T8.4 | Chart.js Integration | Charts for analytics and trends | Chart.js setup + wrappers |
| T8.5 | Advanced Reporting | Custom report builder with filters | Report builder service |
| T8.6 | Export Functionality | Excel/CSV/PDF export for all reports | Export service |
| T8.7 | Notification Center | In-app notification system | Notification service + UI |
| T8.8 | Search Global | Global search across all modules | Search service + UI |
| T8.9 | Audit Log Viewer | View and filter audit logs | Audit log controller + view |
| T8.10 | Settings Panel | System configuration UI | Settings controller + views |

### Phase 9: Testing & Optimization (Week 17-18)

| Task ID | Task | Description | Deliverable |
|---------|------|-------------|-------------|
| T9.1 | Feature Tests | Complete feature test coverage | Test suite |
| T9.2 | Unit Tests | Service and repository unit tests | Unit test suite |
| T9.3 | Performance Testing | Load testing for payroll and reports | Test scripts |
| T9.4 | Security Audit | Penetration testing, code review | Security report |
| T9.5 | Cache Optimization | Redis caching strategy implementation | Cache decorators |
| T9.6 | Query Optimization | Database query profiling and optimization | Optimized queries |
| T9.7 | Queue Monitoring | Failed job handling and monitoring | Queue dashboard |
| T9.8 | Error Handling | Custom error pages and logging | Error handling |
| T9.9 | Documentation | API docs, user manual, deployment guide | Documentation |
| T9.10 | Deployment | Production deployment configuration | Deployment scripts |

---

## 3. Database Schema Summary

### Core Tables
| Table | Purpose | Key Fields |
|-------|---------|------------|
| users | Authentication | email, username, password_hash, salt, status, 2fa |
| employees | Employee data | user_id, department_id, employee_id, hire_date, salary |
| departments | Organization structure | name, parent_id, manager_id |
| roles | RBAC roles | name, slug, description |
| permissions | RBAC permissions | name, slug, module |
| role_permission | Role-permission mapping | role_id, permission_id |
| user_role | User-role mapping | user_id, role_id |
| sessions | Custom session storage | id, user_id, payload, last_activity |
| password_resets | Password reset tokens | user_id, token_hash, expires_at |
| audit_logs | Immutable audit trail | user_id, action, resource, before, after |

### HRM Tables
| Table | Purpose | Key Fields |
|-------|---------|------------|
| payrolls | Payroll runs | period_start, period_end, status, total_amount |
| payslips | Individual payslips | employee_id, payroll_id, gross, deductions, net |
| attendances | Daily attendance | employee_id, date, clock_in, clock_out, status |
| leave_requests | Leave applications | employee_id, type, start_date, end_date, status |
| job_posts | Recruitment postings | title, department_id, status, deadline |
| applicants | Job applicants | job_post_id, name, email, resume, status |
| interviews | Interview records | applicant_id, interviewer_id, scheduled_at, status |
| performance_reviews | Performance data | employee_id, reviewer_id, cycle, rating, status |
| benefits | Benefit plans | name, type, cost, coverage_details |
| benefit_enrollments | Employee enrollments | employee_id, benefit_id, start_date, status |

---

## 4. Route Structure

### Auth Routes
```
GET  /login              -> LoginController@showLoginForm
POST /login              -> LoginController@login
GET  /register           -> RegisterController@showRegistrationForm
POST /register           -> RegisterController@register
GET  /forgot-password    -> ForgotPasswordController@showForm
POST /forgot-password    -> ForgotPasswordController@sendResetLink
GET  /reset-password     -> ResetPasswordController@showForm
POST /reset-password     -> ResetPasswordController@reset
POST /logout             -> LogoutController@logout
```

### Dashboard Routes
```
GET  /dashboard          -> EmployeeDashboardController@index
GET  /manager/dashboard  -> ManagerDashboardController@index
GET  /hr/dashboard       -> HRDashboardController@index
```

### Employee Routes
```
GET    /employees              -> EmployeeController@index
GET    /employees/create       -> EmployeeController@create
POST   /employees              -> EmployeeController@store
GET    /employees/{id}         -> EmployeeController@show
GET    /employees/{id}/edit    -> EmployeeController@edit
PUT    /employees/{id}         -> EmployeeController@update
DELETE /employees/{id}         -> EmployeeController@destroy
GET    /employees/search       -> EmployeeController@search
GET    /org-chart              -> OrgChartController@index
GET    /departments            -> DepartmentController@index
```

### Payroll Routes
```
GET    /payroll                -> PayrollController@index
GET    /payroll/calculate      -> PayrollController@calculateForm
POST   /payroll/calculate      -> PayrollController@runCalculation
GET    /payroll/{id}           -> PayrollController@show
GET    /payslips               -> PayslipController@index
GET    /payslips/{id}          -> PayslipController@show
GET    /payslips/{id}/download -> PayslipController@download
GET    /tax-settings           -> TaxController@index
PUT    /tax-settings           -> TaxController@update
```

### Attendance Routes
```
GET    /attendance             -> AttendanceController@index
POST   /attendance/clock-in    -> AttendanceController@clockIn
POST   /attendance/clock-out   -> AttendanceController@clockOut
GET    /attendance/calendar    -> AttendanceController@calendar
GET    /leave-requests         -> LeaveController@index
GET    /leave-requests/create  -> LeaveController@create
POST   /leave-requests         -> LeaveController@store
PUT    /leave-requests/{id}    -> LeaveController@update
GET    /shifts                 -> ShiftController@index
```

### Recruitment Routes
```
GET    /recruitment/jobs       -> JobPostController@index
GET    /recruitment/jobs/create -> JobPostController@create
POST   /recruitment/jobs       -> JobPostController@store
GET    /recruitment/applicants -> ApplicantController@index
GET    /recruitment/applicants/{id} -> ApplicantController@show
PUT    /recruitment/applicants/{id} -> ApplicantController@update
GET    /recruitment/interviews -> InterviewController@index
POST   /recruitment/interviews -> InterviewController@store
```

### Performance Routes
```
GET    /performance/reviews    -> ReviewController@index
GET    /performance/reviews/create -> ReviewController@create
POST   /performance/reviews    -> ReviewController@store
GET    /performance/goals    -> GoalController@index
POST   /performance/goals    -> GoalController@store
GET    /performance/feedback   -> FeedbackController@index
POST   /performance/feedback   -> FeedbackController@store
```

### Benefits Routes
```
GET    /benefits               -> BenefitController@index
GET    /benefits/enroll         -> EnrollmentController@create
POST   /benefits/enroll         -> EnrollmentController@store
GET    /benefits/my-benefits    -> EnrollmentController@myBenefits
```

---

## 5. Middleware Assignment

| Route Group | Middleware | Description |
|-------------|-----------|-------------|
| All auth routes | web | Standard web middleware |
| All dashboard + module routes | custom.auth | Custom authentication check |
| /hr/* | role:hr_admin | HR admin role required |
| /manager/* | role:manager | Manager role required |
| /payroll/calculate | permission:run_payroll | Specific permission |
| /employees/create | permission:create_employee | Specific permission |
| /employees/{id}/edit | permission:edit_employee | Specific permission |
| /employees/{id} | permission:view_employee | Specific permission |
| /leave-requests/approve | permission:approve_leave | Specific permission |
| All POST/PUT/DELETE | audit.log | Log action to audit trail |

---

## 6. jQuery Component Structure

```javascript
// resources/js/app.js - Main entry point
$(document).ready(function() {
    // Initialize CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize components
    HRM.init();
});

// resources/js/components/modal.js
const HRMModal = {
    open: function(id) {
        $(`#${id}`).removeClass('hidden').addClass('flex');
    },
    close: function(id) {
        $(`#${id}`).addClass('hidden').removeClass('flex');
    }
};

// resources/js/components/datatable.js
const HRMDataTable = {
    init: function(selector, options) {
        $(selector).DataTable({
            processing: true,
            serverSide: options.ajax ? true : false,
            ajax: options.ajax || null,
            columns: options.columns,
            pageLength: 25,
            language: {
                search: "",
                searchPlaceholder: "Search..."
            },
            dom: '<"flex justify-between items-center mb-4"<"flex-1"f><"flex-none"l>>rt<"flex justify-between items-center mt-4"<"flex-1"i><"flex-none"p>>',
            ...options
        });
    }
};

// resources/js/components/notification.js
const HRMNotification = {
    success: function(message) {
        this.show(message, 'success');
    },
    error: function(message) {
        this.show(message, 'error');
    },
    show: function(message, type) {
        const colors = {
            success: 'bg-secondary-50 border-secondary-200 text-secondary-800',
            error: 'bg-danger-50 border-danger-200 text-danger-800'
        };

        const html = `
            <div class="fixed top-4 right-4 z-50 rounded-lg p-4 border ${colors[type]} shadow-lg max-w-sm">
                <p class="text-sm font-medium">${message}</p>
            </div>
        `;

        const $el = $(html).appendTo('body');
        setTimeout(() => $el.fadeOut(() => $el.remove()), 5000);
    }
};

// resources/js/pages/employee.js
const EmployeePage = {
    init: function() {
        this.initDataTable();
        this.bindEvents();
    },
    initDataTable: function() {
        HRMDataTable.init('#employees-table', {
            ajax: '/employees/data',
            columns: [
                { data: 'employee_id', title: 'ID' },
                { data: 'full_name', title: 'Name' },
                { data: 'department', title: 'Department' },
                { data: 'designation', title: 'Designation' },
                { data: 'status', title: 'Status' },
                { data: 'actions', title: 'Actions', orderable: false }
            ]
        });
    },
    bindEvents: function() {
        // Delete confirmation
        $(document).on('click', '.delete-employee', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this employee?')) {
                $.ajax({
                    url: `/employees/${id}`,
                    type: 'DELETE',
                    success: function() {
                        HRMNotification.success('Employee deleted successfully');
                        $('#employees-table').DataTable().ajax.reload();
                    },
                    error: function() {
                        HRMNotification.error('Failed to delete employee');
                    }
                });
            }
        });
    }
};
```

---

## 7. Configuration Files

### config/hrm.php
```php
<?php

return [
    'company_name' => env('HRM_COMPANY_NAME', 'Company Name'),
    'company_logo' => env('HRM_COMPANY_LOGO', null),

    'payroll' => [
        'default_currency' => env('HRM_CURRENCY', 'USD'),
        'pay_date' => env('HRM_PAY_DATE', 'last_day'),
        'overime_multiplier' => 1.5,
        'tax_calculation_method' => 'progressive',
    ],

    'attendance' => [
        'work_start_time' => '09:00',
        'work_end_time' => '17:00',
        'grace_period_minutes' => 15,
        'late_threshold_minutes' => 30,
    ],

    'leave' => [
        'annual_leave_days' => 20,
        'sick_leave_days' => 10,
        'carry_forward_limit' => 5,
        'min_notice_days' => 7,
    ],

    'auth' => [
        'max_login_attempts' => 5,
        'lockout_duration' => 30,
        'password_expiry_days' => 90,
        'session_timeout' => 120,
        'require_2fa' => false,
    ],

    'notifications' => [
        'email_enabled' => true,
        'sms_enabled' => false,
        'in_app_enabled' => true,
    ],
];
```

### config/permissions.php
```php
<?php

return [
    'modules' => [
        'employee' => [
            'view_employee',
            'create_employee',
            'edit_employee',
            'delete_employee',
            'export_employees',
        ],
        'payroll' => [
            'view_payroll',
            'run_payroll',
            'edit_payroll',
            'view_payslip',
            'download_payslip',
        ],
        'attendance' => [
            'view_attendance',
            'clock_in_out',
            'view_leave_requests',
            'apply_leave',
            'approve_leave',
            'manage_shifts',
        ],
        'recruitment' => [
            'view_jobs',
            'create_job',
            'edit_job',
            'delete_job',
            'view_applicants',
            'manage_applicants',
            'schedule_interviews',
        ],
        'performance' => [
            'view_reviews',
            'create_review',
            'edit_review',
            'view_goals',
            'manage_goals',
            'give_feedback',
        ],
        'benefits' => [
            'view_benefits',
            'enroll_benefits',
            'manage_benefits',
            'view_enrollments',
        ],
        'settings' => [
            'view_settings',
            'edit_settings',
            'manage_users',
            'manage_roles',
            'view_audit_logs',
        ],
    ],

    'roles' => [
        'super_admin' => ['*'],
        'hr_admin' => [
            'employee.*',
            'payroll.*',
            'attendance.*',
            'recruitment.*',
            'performance.*',
            'benefits.*',
            'settings.*',
        ],
        'hr_manager' => [
            'employee.view_employee', 'employee.create_employee', 'employee.edit_employee',
            'payroll.view_payroll', 'payroll.run_payroll',
            'attendance.*',
            'recruitment.*',
            'performance.view_reviews', 'performance.create_review', 'performance.edit_review',
            'benefits.*',
        ],
        'manager' => [
            'employee.view_employee',
            'attendance.view_attendance', 'attendance.approve_leave',
            'performance.view_reviews', 'performance.create_review', 'performance.give_feedback',
            'recruitment.view_applicants', 'recruitment.manage_applicants', 'recruitment.schedule_interviews',
        ],
        'employee' => [
            'employee.view_employee',
            'payroll.view_payslip', 'payroll.download_payslip',
            'attendance.clock_in_out', 'attendance.apply_leave', 'attendance.view_leave_requests',
            'performance.view_goals', 'performance.give_feedback',
            'benefits.view_benefits', 'benefits.enroll_benefits',
        ],
    ],
];
```

---

## 8. Implementation Checklist

### Week 1-2: Foundation
- [ ] Laravel 11 installed with required packages
- [ ] Tailwind CSS configured with custom theme
- [ ] Database migrations created and run
- [ ] Custom auth service implemented
- [ ] All auth controllers working
- [ ] Auth middleware protecting routes
- [ ] Login/register/forgot-password views styled
- [ ] Session management working
- [ ] Seeders populating base data
- [ ] Auth tests passing

### Week 3-4: Employee Module
- [ ] Employee CRUD complete
- [ ] Department tree structure working
- [ ] Org chart visualization
- [ ] AJAX search functional
- [ ] File upload for photos
- [ ] Repository pattern implemented
- [ ] Events firing correctly

### Week 5-6: Payroll Module
- [ ] Payroll calculation engine
- [ ] Tax rules configurable
- [ ] Payslip PDF generation
- [ ] Queue processing payroll
- [ ] Audit trail for changes
- [ ] Monthly reports available

### Week 7-8: Attendance Module
- [ ] Clock in/out working
- [ ] Leave request workflow
- [ ] Calendar view
- [ ] Leave balance tracking
- [ ] Overtime calculation
- [ ] Integration with payroll

### Week 9-10: Recruitment Module
- [ ] Job posting CRUD
- [ ] Applicant tracking pipeline
- [ ] Interview scheduling
- [ ] Resume upload
- [ ] Email notifications
- [ ] Public career portal

### Week 11-12: Performance Module
- [ ] Review cycle management
- [ ] 360 feedback collection
- [ ] Goal setting and tracking
- [ ] Custom form templates
- [ ] Performance charts
- [ ] Calibration tool

### Week 13-14: Benefits Module
- [ ] Benefit catalog
- [ ] Enrollment workflow
- [ ] Deduction calculations
- [ ] Provider integrations
- [ ] COBRA management
- [ ] Payroll integration

### Week 15-16: Dashboard & Reporting
- [ ] Role-based dashboards
- [ ] KPI widgets
- [ ] DataTables on all lists
- [ ] Chart.js charts
- [ ] Report builder
- [ ] Export functionality
- [ ] Notification center
- [ ] Global search
- [ ] Audit log viewer
- [ ] Settings panel

### Week 17-18: Testing & Deployment
- [ ] Feature test coverage >80%
- [ ] Unit tests for services
- [ ] Load testing complete
- [ ] Security audit passed
- [ ] Cache optimization done
- [ ] Query optimization done
- [ ] Queue monitoring ready
- [ ] Error handling complete
- [ ] Documentation written
- [ ] Production deployed
