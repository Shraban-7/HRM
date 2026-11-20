<header class="bg-white shadow px-6 py-3.5 flex justify-between items-center">
    <div class="flex items-center space-x-4">
        <button id="sidebarToggle" class="text-gray-600 hover:text-gray-900 focus:outline-none">
            <i class="fas fa-bars text-lg"></i>
        </button>
        <h2 class="text-lg font-bold text-gray-800">@yield('title', 'Dashboard')</h2>
        
        <!-- Command Palette Trigger Button -->
        <button type="button" onclick="openCommandPalette()" class="hidden md:flex items-center space-x-2 px-3 py-1.5 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-xs text-gray-500 transition ml-2">
            <i class="fas fa-search text-gray-400"></i>
            <span>Quick Jump...</span>
            <kbd class="bg-white border border-gray-200 rounded px-1.5 py-0.5 text-[10px] font-mono text-gray-400">Ctrl+K</kbd>
        </button>
    </div>
    <div class="flex items-center space-x-4">
        <a href="{{ route('notifications.index') }}" class="relative text-gray-600 hover:text-gray-900" title="Notifications">
            <i class="fas fa-bell text-lg"></i>
            <span id="notificationBadge" class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[10px] font-bold rounded-full px-1.5 py-0.2 hidden">0</span>
        </a>
        <div class="h-4 w-px bg-gray-200"></div>
        <span class="text-xs font-semibold text-gray-700 flex items-center space-x-1.5">
            <i class="fas fa-user-circle text-base text-gray-400"></i>
            <span>{{ session('user_name', 'Guest') }}</span>
        </span>
        <form action="{{ route('auth.logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-semibold flex items-center space-x-1">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</header>
