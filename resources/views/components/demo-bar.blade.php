<div class="bg-gradient-to-r from-indigo-900 via-purple-900 to-gray-900 text-white px-4 py-2 text-xs flex flex-wrap items-center justify-between border-b border-indigo-700 shadow-md z-50">
    <div class="flex items-center space-x-2 mr-4">
        <span class="bg-amber-400 text-gray-950 font-bold px-2 py-0.5 rounded text-[10px] tracking-wide uppercase">Interactive Demo</span>
        <span class="text-gray-300 hidden md:inline">Test-drive role experiences with 1-click preview:</span>
    </div>
    <div class="flex items-center space-x-2 overflow-x-auto py-1">
        <form action="{{ route('demo.switch', 'super_admin') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-2.5 py-1 rounded bg-indigo-600 hover:bg-indigo-500 font-medium transition shadow-sm flex items-center space-x-1">
                <i class="fas fa-shield-alt text-amber-300 text-xs"></i>
                <span>Super Admin</span>
            </button>
        </form>
        <form action="{{ route('demo.switch', 'hr_admin') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-2.5 py-1 rounded bg-teal-600 hover:bg-teal-500 font-medium transition shadow-sm flex items-center space-x-1">
                <i class="fas fa-user-tie text-teal-200 text-xs"></i>
                <span>HR Manager</span>
            </button>
        </form>
        <form action="{{ route('demo.switch', 'manager') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-2.5 py-1 rounded bg-blue-600 hover:bg-blue-500 font-medium transition shadow-sm flex items-center space-x-1">
                <i class="fas fa-briefcase text-blue-200 text-xs"></i>
                <span>Dept Manager</span>
            </button>
        </form>
        <form action="{{ route('demo.switch', 'employee') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-2.5 py-1 rounded bg-emerald-600 hover:bg-emerald-500 font-medium transition shadow-sm flex items-center space-x-1">
                <i class="fas fa-user text-emerald-200 text-xs"></i>
                <span>Employee</span>
            </button>
        </form>
    </div>
</div>
