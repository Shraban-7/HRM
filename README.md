# Human Resource Management (HRM) System

> A modern, enterprise-grade Human Resource Management system built with **Laravel 13**, **PHP 8.3**, **Blade**, **Tailwind CSS v4**, **Vite 8**, and **jQuery 3.7**.

![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-v4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-v8.0-646CFF?style=for-the-badge&logo=vite&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)

---

## 📌 Overview

Managing human resources, payroll calculations, attendance tracking, and performance reviews across growing organizations often requires integrating multiple disparate tools. 

This **HRM System** provides a single unified, secure, layered monolith solution. It simplifies employee onboarding, automates multi-tier progressive tax and payroll calculations, generates downloadable PDF payslips, tracks employee shifts and leave requests, hosts a public recruitment portal, manages performance review cycles, and logs full audit trails for compliance.

---

## 📋 Table of Contents

- [Overview](#-overview)
- [Key Features](#-key-features)
- [Tech Stack](#-tech-stack)
- [Architecture & Design Patterns](#-architecture--design-patterns)
- [Prerequisites](#-prerequisites)
- [Installation](#-installation)
- [Usage & Development](#-usage--development)
- [Default Demo Credentials](#-default-demo-credentials)
- [Environment Configuration](#-environment-configuration)
- [Folder Structure](#-folder-structure)
- [Testing](#-testing)
- [Contributing](#-contributing)
- [License](#-license)
- [Contact & Support](#-contact--support)

---

## ✨ Key Features

### 🔐 1. Authentication & Role-Based Access Control (RBAC)
- Custom session authentication (`CustomAuthService`) with brute-force rate-limiting (`throttle:6,1`).
- Role-based permissions (`super_admin`, `hr_admin`, `manager`, `employee`).
- Secret password hashing pepper support (`HRM_PEPPER`) and account lockout.

### 👥 2. Employee Lifecycle & Organization Structure
- Onboarding to offboarding/termination workflows.
- Personal information, profile photos, emergency contacts, and banking details.
- Interactive Visual Org Chart (`/org-chart`) with recursive department trees and cache invalidation.

### 💰 3. Automated Payroll & Tax Engine
- Progressive multi-tier income tax calculation engine (`TaxEngine`).
- Pre-tax and post-tax deductions (401k, health benefits, custom deductions).
- Asynchronous batch payroll processing via queued jobs (`ProcessPayrollJob`).
- Downloadable PDF payslips (`dompdf`).

### ⏱️ 4. Attendance, Shifts & Leave Workflows
- Real-time Clock In / Clock Out tracking with automated duration calculations.
- Shift scheduling with overtime calculation.
- Leave management (Paid, Sick, Unpaid) with multi-level approval workflows.

### 💼 5. ATS Recruitment & Public Career Portal
- Internal Job posting creation and Applicant Tracking System (ATS).
- Public-facing Career Portal (`/careers`) for candidate applications.
- Interview scheduling and one-click hiring workflow.

### 📊 6. Performance Management & Reviews
- Multi-directional review cycles (Self, Peer, Manager).
- Goal setting, continuous feedback, and performance calibration dashboard.
- Automated performance bonus calculations based on rating scales.

### 🏥 7. Benefits Catalog & COBRA Compliance
- Benefit plan administration (Health, Dental, Vision, Retirement).
- Employee enrollment lifecycle and employer/employee cost splitting.
- COBRA continuation coverage management and termination tracking.

### 🔔 8. Notifications & Security Audit Trail
- In-app notification center and background email delivery.
- Comprehensive security audit log (`AuditLog` middleware) capturing IP addresses, actions, user agents, and affected models.

---

## 🛠️ Tech Stack

| Layer | Technology | Description |
|---|---|---|
| **Backend Framework** | [Laravel 13](https://laravel.com) | Core MVC web framework (PHP 8.3+) |
| **Frontend Rendering** | [Blade Engine](https://laravel.com/docs/blade) | Server-rendered HTML templates |
| **Styling** | [Tailwind CSS v4](https://tailwindcss.com) | Modern utility-first CSS design system |
| **Client Scripting** | [jQuery 3.7](https://jquery.com) | Interactive UI & AJAX requests |
| **Asset Bundler** | [Vite 8](https://vitejs.dev) | Lightning-fast frontend asset compilation |
| **Database** | MySQL / PostgreSQL / SQLite | SQLite by default for development & testing |
| **Cache & Queue** | Database / Redis | Database driver by default; Redis supported |
| **PDF Engine** | Dompdf | Downloadable PDF payslip generator |
| **Testing** | PHPUnit 12 | Automated unit and feature testing framework |

---

## 📐 Architecture & Design Patterns

The project follows a **Layered Monolith Architecture**:

```
┌─────────────────────────────────────────────────────────┐
│                    Web Routes (web.php)                 │
├─────────────────────────────────────────────────────────┤
│                   Controllers (Thin)                    │
│            Form Request Validation & Dispatch           │
├─────────────────────────────────────────────────────────┤
│                    Services (Fat)                       │
│             Business Logic & Domain Rules               │
├───────────────┬───────────────────┬─────────────────────┤
│ Repositories  │   Domain Events   │   Policies / Gates  │
│(Data Access)  │   & Queue Jobs    │    (Authorization)   │
├───────────────┴───────────────────┴─────────────────────┤
│                    Eloquent Models                      │
├─────────────────────────────────────────────────────────┤
│                  Database & Cache Store                 │
└─────────────────────────────────────────────────────────┘
```

- **Services**: Encapsulate domain rules (`PayrollCalculator`, `TaxEngine`, `AttendanceProcessor`, `CobraService`).
- **Repositories**: Abstract persistence layer behind interface contracts in `app/Repositories/Contracts/` backed by cached decorators.
- **Events & Queue Jobs**: Offload intensive processes (emails, PDF payslips, audit records, batch payroll) to background queues.

---

## 💻 Prerequisites

Ensure your development environment meets the following requirements:

- **PHP**: `^8.3` (Extensions required: `pdo`, `sqlite3` or `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`)
- **Composer**: `^2.x`
- **Node.js**: `^18.x` or `^20.x` & **npm** `^9.x`
- **Database**: SQLite (default), MySQL `^8.0`, or PostgreSQL `^15`

---

## 🚀 Installation

### Option 1: Automated Setup (Recommended)

Run the single composer command to install dependencies, copy environment files, generate application keys, run migrations with seeders, and build assets:

```bash
composer setup
```

### Option 2: Manual Setup

1. **Clone the repository**:
   ```bash
   git clone https://github.com/Shraban-7/HRM.git
   cd HRM
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**:
   ```bash
   npm install
   ```

4. **Configure Environment Variables**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Run Migrations & Seed Database**:
   ```bash
   php artisan migrate --seed
   ```

6. **Build Frontend Assets**:
   ```bash
   npm run build
   ```

---

## ⚡ Usage & Development

### Running the Development Environment

Launch the web server, background queue worker, log tailing, and Vite hot-reload concurrently:

```bash
composer dev
```

The application will be accessible at **`http://localhost:8000`**.

### Standalone Commands

If you prefer running services in separate terminal windows:

```bash
# Start Web Server
php artisan serve

# Start Queue Listener
php artisan queue:listen --tries=3

# Start Asset Hot-Reloading
npm run dev
```

### Operational Artisan Commands

```bash
# Benchmark heaviest database & calculation operations
php artisan app:load-test

# Monitor background queue health
php artisan queue:monitor

# Automatically retry all failed queued jobs
php artisan queue:monitor --retry-failed
```

---

## 🔑 Default Demo Credentials

After seeding the database (`php artisan db:seed`), use these demo accounts to log in:

| Role | Email | Password | Permissions |
|---|---|---|---|
| **Super Admin** | `admin@hrm.com` | `password` | Full system access, audit logs, configuration |
| **Employee** | `user@hrm.com` | `password` | Employee self-service dashboard |

---

## ⚙️ Environment Configuration

Key configuration parameters defined in `.env`:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database

MAIL_MAILER=log
HRM_PEPPER=
```

Business logic parameters (tax brackets, leave quotas, deduction percentages) are configured in `config/hrm.php`.

---

## 📁 Folder Structure

```
hrm-app/
├── app/
│   ├── Console/Commands/       # Custom CLI commands (load test, queue monitor)
│   ├── Events/                 # Domain events (EmployeeHired, PayrollProcessed)
│   ├── Helpers/                # Core helpers (Encryption, Hashes)
│   ├── Http/
│   │   ├── Controllers/        # Thin controllers handling requests & responses
│   │   ├── Middleware/         # CustomAuth, RBAC, AuditLog, SecurityHeaders
│   │   └── Requests/           # Form Request validation classes
│   ├── Jobs/                   # Queued background jobs (Payroll, Payslips, Reports)
│   ├── Listeners/              # Event listeners handling domain events
│   ├── Models/                 # Eloquent ORM models
│   ├── Policies/               # Authorization policy classes
│   ├── Providers/              # Service providers & repository interface bindings
│   ├── Repositories/           # Repository contracts, Eloquent logic & cache decorators
│   └── Services/               # Core business logic services
├── config/                     # Application & HRM system settings (`hrm.php`)
├── database/
│   ├── factories/              # Model factories for testing
│   ├── migrations/             # Database migration schemas
│   └── seeders/                # Database seeders (Roles, Admin, Departments)
├── docs/                       # Architectural & Deployment documentation
│   ├── architecture.md
│   └── deployment.md
├── public/                     # Compiled web assets & index.php
├── resources/
│   ├── css/                    # Tailwind CSS source files
│   ├── js/                     # JavaScript modules & jQuery handlers
│   └── views/                  # Blade views & layouts
├── routes/
│   └── web.php                 # Web application routes
├── tests/                      # Automated Feature & Unit test suites
├── composer.json               # PHP dependencies & composer scripts
├── package.json                # Frontend dependencies & Vite scripts
└── vite.config.js              # Vite bundler configuration
```

---

## 🧪 Testing

Run the full automated test suite using PHPUnit:

```bash
composer test
```

Or using Artisan:

```bash
php artisan test
```

Tests run against an isolated in-memory SQLite database (`phpunit.xml`) covering all core domain modules.

---

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines when submitting pull requests:

1. **Fork the Repository** and create a feature branch (`git checkout -b feature/amazing-feature`).
2. **Adhere to Code Standards**: Format PHP code using Laravel Pint (`vendor/bin/pint`).
3. **Write Tests**: Ensure new features or bug fixes include corresponding unit/feature tests.
4. **Commit Changes**: Use clean, descriptive commit messages (`git commit -m 'Add amazing feature'`).
5. **Push & Open PR**: Push to your branch and submit a Pull Request targeting `main`.

---

## 📄 License

Distributed under the **MIT License**. See [`LICENSE`](LICENSE).

---

## 📬 Contact & Support

- **Author**: [Shraban-7](https://github.com/Shraban-7)
- **Repository**: [https://github.com/Shraban-7/HRM](https://github.com/Shraban-7/HRM)
- **Documentation**: See [docs/architecture.md](docs/architecture.md) and [docs/deployment.md](docs/deployment.md) for architectural and deployment details.