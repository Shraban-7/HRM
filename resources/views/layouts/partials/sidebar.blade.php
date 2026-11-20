<aside id="sidebar" class="w-64 bg-gray-900 text-white flex-shrink-0 h-screen overflow-y-auto transition-all duration-300">
    <div class="p-4 text-xl font-bold border-b border-gray-800 whitespace-nowrap overflow-hidden flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5">
            <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-sm">
                <i class="fas fa-users-viewfinder"></i>
            </div>
            <span class="sidebar-text text-lg tracking-tight font-extrabold">Nexus<span class="text-indigo-400">HRM</span></span>
        </a>
    </div>
    <nav class="p-4 space-y-1 pb-16 text-xs">
        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-chart-line mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Dashboard</span>
        </a>

        <!-- CORE HR -->
        <div class="sidebar-section-title text-[11px] font-bold text-gray-500 uppercase mt-4 mb-1 px-2 whitespace-nowrap overflow-hidden">
            <span class="sidebar-text">Core Workforce</span>
            <span class="sidebar-icon-only hidden text-center">CW</span>
        </div>
        <a href="{{ route('employees.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('employees.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-user-friends mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Employee Directory</span>
        </a>
        <a href="{{ route('departments.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('departments.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-building mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Departments</span>
        </a>
        <a href="{{ route('org-chart') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('org-chart') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-sitemap mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Visual Org Chart</span>
        </a>
        <a href="{{ route('onboarding.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('onboarding.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-user-plus mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Onboarding</span>
        </a>
        <a href="{{ route('exit.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('exit.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-user-minus mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Exit Offboarding</span>
        </a>

        <!-- TIME & ATTENDANCE -->
        <div class="sidebar-section-title text-[11px] font-bold text-gray-500 uppercase mt-4 mb-1 px-2 whitespace-nowrap overflow-hidden">
            <span class="sidebar-text">Time & Attendance</span>
            <span class="sidebar-icon-only hidden text-center">TA</span>
        </div>
        <a href="{{ route('attendance.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('attendance.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-fingerprint mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Clock-in Ledger</span>
        </a>
        <a href="{{ route('shifts.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('shifts.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-calendar-day mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Shift Rosters</span>
        </a>
        <a href="{{ route('leave.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('leave.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-umbrella-beach mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Leave Requests</span>
        </a>

        <!-- PAYROLL & FINANCE -->
        <div class="sidebar-section-title text-[11px] font-bold text-gray-500 uppercase mt-4 mb-1 px-2 whitespace-nowrap overflow-hidden">
            <span class="sidebar-text">Payroll & Finance</span>
            <span class="sidebar-icon-only hidden text-center">PF</span>
        </div>
        <a href="{{ route('payrolls.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('payrolls.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-calculator mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Payroll Processing</span>
        </a>
        <a href="{{ route('payslips.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('payslips.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-file-invoice-dollar mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Employee Payslips</span>
        </a>
        <a href="{{ route('expenses.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('expenses.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-receipt mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Expense Claims</span>
        </a>
        <a href="{{ route('loans.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('loans.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-hand-holding-usd mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Loans & Advances</span>
        </a>

        <!-- TALENT & RECRUITMENT -->
        <div class="sidebar-section-title text-[11px] font-bold text-gray-500 uppercase mt-4 mb-1 px-2 whitespace-nowrap overflow-hidden">
            <span class="sidebar-text">Talent & ATS</span>
            <span class="sidebar-icon-only hidden text-center">TR</span>
        </div>
        <a href="{{ route('jobs.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('jobs.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-briefcase mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Job Postings</span>
        </a>
        <a href="{{ route('applicants.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('applicants.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-user-tie mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Candidate Pipeline</span>
        </a>
        <a href="{{ route('interviews.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('interviews.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-calendar-check mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Interview Schedule</span>
        </a>

        <!-- PERFORMANCE & REVIEWS -->
        <div class="sidebar-section-title text-[11px] font-bold text-gray-500 uppercase mt-4 mb-1 px-2 whitespace-nowrap overflow-hidden">
            <span class="sidebar-text">Performance</span>
            <span class="sidebar-icon-only hidden text-center">PR</span>
        </div>
        <a href="{{ route('reviews.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('reviews.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-star mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Review Cycles</span>
        </a>
        <a href="{{ route('goals.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('goals.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-bullseye mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">SMART Goals</span>
        </a>

        <!-- COMPANY ASSETS & DOSSIERS -->
        <div class="sidebar-section-title text-[11px] font-bold text-gray-500 uppercase mt-4 mb-1 px-2 whitespace-nowrap overflow-hidden">
            <span class="sidebar-text">Operations</span>
            <span class="sidebar-icon-only hidden text-center">OP</span>
        </div>
        <a href="{{ route('assets.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('assets.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-laptop-code mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Hardware Assets</span>
        </a>
        <a href="{{ route('documents.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('documents.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-folder-open mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Digital Documents</span>
        </a>
        <a href="{{ route('benefits.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('benefits.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-shield-heart mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Benefits Catalog</span>
        </a>
        <a href="{{ route('helpdesk.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('helpdesk.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-headset mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Help Desk Tickets</span>
        </a>

        <!-- SYSTEM & ADMIN -->
        <div class="sidebar-section-title text-[11px] font-bold text-gray-500 uppercase mt-4 mb-1 px-2 whitespace-nowrap overflow-hidden">
            <span class="sidebar-text">Administration</span>
            <span class="sidebar-icon-only hidden text-center">AD</span>
        </div>
        <a href="{{ route('reports.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('reports.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-chart-pie mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Executive Reports</span>
        </a>
        <a href="{{ route('settings.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('settings.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-sliders-h mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Company Settings</span>
        </a>
        <a href="{{ route('audit.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-800 transition whitespace-nowrap overflow-hidden {{ request()->routeIs('audit.*') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-300' }}">
            <i class="fas fa-history mr-3 w-5 text-center flex-shrink-0"></i><span class="sidebar-text">Audit Trail</span>
        </a>
    </nav>
</aside>
