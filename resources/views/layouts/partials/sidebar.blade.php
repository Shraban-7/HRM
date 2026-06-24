<aside class="w-64 bg-gray-900 text-white flex-shrink-0">
    <div class="p-4 text-xl font-bold border-b border-gray-700">
        <i class="fas fa-users mr-2"></i>HRM System
    </div>
    <nav class="p-4 space-y-2">
        <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
        </a>
        <div class="text-sm text-gray-400 uppercase mt-4 mb-2 px-2">Employees</div>
        <a href="{{ route('employees.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('employees.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-user-friends mr-2"></i>Employees
        </a>
        <a href="{{ route('departments.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('departments.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-building mr-2"></i>Departments
        </a>
        <a href="{{ route('org-chart') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('org-chart') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-sitemap mr-2"></i>Org Chart
        </a>
        <div class="text-sm text-gray-400 uppercase mt-4 mb-2 px-2">Payroll</div>
        <a href="{{ route('payrolls.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('payrolls.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-money-bill mr-2"></i>Payroll
        </a>
        <a href="{{ route('payslips.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('payslips.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-file-invoice mr-2"></i>Payslips
        </a>
        <div class="text-sm text-gray-400 uppercase mt-4 mb-2 px-2">Attendance</div>
        <a href="{{ route('attendance.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('attendance.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-clock mr-2"></i>Attendance
        </a>
        <a href="{{ route('leave.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('leave.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-calendar-alt mr-2"></i>Leave
        </a>
        <div class="text-sm text-gray-400 uppercase mt-4 mb-2 px-2">Recruitment</div>
        <a href="{{ route('jobs.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('jobs.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-briefcase mr-2"></i>Jobs
        </a>
        <a href="{{ route('applicants.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('applicants.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-users mr-2"></i>Applicants
        </a>
        <a href="{{ route('interviews.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('interviews.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-handshake mr-2"></i>Interviews
        </a>
        <div class="text-sm text-gray-400 uppercase mt-4 mb-2 px-2">Performance</div>
        <a href="{{ route('reviews.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('reviews.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-star mr-2"></i>Reviews
        </a>
        <a href="{{ route('goals.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('goals.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-bullseye mr-2"></i>Goals
        </a>
        <a href="{{ route('feedback.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('feedback.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-comment mr-2"></i>Feedback
        </a>
        <div class="text-sm text-gray-400 uppercase mt-4 mb-2 px-2">Benefits</div>
        <a href="{{ route('benefits.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('benefits.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-gift mr-2"></i>Benefits
        </a>
        <a href="{{ route('enrollments.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('enrollments.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-check-circle mr-2"></i>Enrollments
        </a>
        <div class="text-sm text-gray-400 uppercase mt-4 mb-2 px-2">System</div>
        <a href="{{ route('audit.index') }}" class="block px-4 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('audit.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-history mr-2"></i>Audit Logs
        </a>
    </nav>
</aside>