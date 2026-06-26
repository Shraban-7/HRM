<aside id="sidebar" class="w-64 bg-gray-900 text-white flex-shrink-0 h-screen overflow-y-auto transition-all duration-300">
    <div class="p-4 text-xl font-bold border-b border-gray-700 whitespace-nowrap overflow-hidden">
        <i class="fas fa-users mr-2"></i><span class="sidebar-text">HRM System</span>
    </div>
    <nav class="p-4 space-y-2 pb-8">
        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap overflow-hidden {{ request()->routeIs('dashboard') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-tachometer-alt mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Dashboard</span>
        </a>
        <div class="sidebar-section-title text-sm text-gray-400 uppercase mt-4 mb-2 px-2 whitespace-nowrap overflow-hidden">
            <span class="sidebar-text">Employees</span>
            <span class="sidebar-icon-only hidden text-xs text-center">EM</span>
        </div>
        <a href="{{ route('employees.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap overflow-hidden {{ request()->routeIs('employees.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-user-friends mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Employees</span>
        </a>
        <a href="{{ route('departments.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap overflow-hidden {{ request()->routeIs('departments.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-building mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Departments</span>
        </a>
        <a href="{{ route('org-chart') }}" class="flex items-center px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap overflow-hidden {{ request()->routeIs('org-chart') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-sitemap mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Org Chart</span>
        </a>
        <div class="sidebar-section-title text-sm text-gray-400 uppercase mt-4 mb-2 px-2 whitespace-nowrap overflow-hidden">
            <span class="sidebar-text">Payroll</span>
            <span class="sidebar-icon-only hidden text-xs text-center">PR</span>
        </div>
        <a href="{{ route('payrolls.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap overflow-hidden {{ request()->routeIs('payrolls.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-money-bill mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Payroll</span>
        </a>
        <a href="{{ route('payslips.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap overflow-hidden {{ request()->routeIs('payslips.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-file-invoice mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Payslips</span>
        </a>
        <div class="sidebar-section-title text-sm text-gray-400 uppercase mt-4 mb-2 px-2 whitespace-nowrap overflow-hidden">
            <span class="sidebar-text">Attendance</span>
            <span class="sidebar-icon-only hidden text-xs text-center">AT</span>
        </div>
        <a href="{{ route('attendance.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap overflow-hidden {{ request()->routeIs('attendance.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-clock mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Attendance</span>
        </a>
        <a href="{{ route('leave.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap overflow-hidden {{ request()->routeIs('leave.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-calendar-alt mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Leave</span>
        </a>
        <div class="sidebar-section-title text-sm text-gray-400 uppercase mt-4 mb-2 px-2 whitespace-nowrap overflow-hidden">
            <span class="sidebar-text">Recruitment</span>
            <span class="sidebar-icon-only hidden text-xs text-center">RC</span>
        </div>
        <a href="{{ route('jobs.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap overflow-hidden {{ request()->routeIs('jobs.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-briefcase mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Jobs</span>
        </a>
        <a href="{{ route('applicants.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap overflow-hidden {{ request()->routeIs('applicants.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-users mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Applicants</span>
        </a>
        <a href="{{ route('interviews.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap overflow-hidden {{ request()->routeIs('interviews.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-handshake mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Interviews</span>
        </a>
        <div class="sidebar-section-title text-sm text-gray-400 uppercase mt-4 mb-2 px-2 whitespace-nowrap overflow-hidden">
            <span class="sidebar-text">Performance</span>
            <span class="sidebar-icon-only hidden text-xs text-center">PF</span>
        </div>
        <a href="{{ route('reviews.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap overflow-hidden {{ request()->routeIs('reviews.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-star mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Reviews</span>
        </a>
        <a href="{{ route('goals.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap overflow-hidden {{ request()->routeIs('goals.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-bullseye mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Goals</span>
        </a>
        <a href="{{ route('feedback.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap overflow-hidden {{ request()->routeIs('feedback.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-comment mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Feedback</span>
        </a>
        <div class="sidebar-section-title text-sm text-gray-400 uppercase mt-4 mb-2 px-2 whitespace-nowrap overflow-hidden">
            <span class="sidebar-text">Benefits</span>
            <span class="sidebar-icon-only hidden text-xs text-center">BN</span>
        </div>
        <a href="{{ route('benefits.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap overflow-hidden {{ request()->routeIs('benefits.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-gift mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Benefits</span>
        </a>
        <a href="{{ route('enrollments.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap overflow-hidden {{ request()->routeIs('enrollments.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-check-circle mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Enrollments</span>
        </a>
        <div class="sidebar-section-title text-sm text-gray-400 uppercase mt-4 mb-2 px-2 whitespace-nowrap overflow-hidden">
            <span class="sidebar-text">System</span>
            <span class="sidebar-icon-only hidden text-xs text-center">SY</span>
        </div>
        <a href="{{ route('audit.index') }}" class="flex items-center px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap overflow-hidden {{ request()->routeIs('audit.*') ? 'bg-gray-700' : '' }}">
            <i class="fas fa-history mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Audit Logs</span>
        </a>
    </nav>
</aside>