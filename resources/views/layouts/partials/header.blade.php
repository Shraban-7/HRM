<header class="bg-white shadow px-6 py-4 flex justify-between items-center">
    <h2 class="text-xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h2>
    <div class="flex items-center space-x-4">
        <span class="text-gray-600"><i class="fas fa-user mr-2"></i>{{ session('user_name', 'Guest') }}</span>
        <form action="{{ route('auth.logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="text-red-600 hover:text-red-800">
                <i class="fas fa-sign-out-alt mr-1"></i>Logout
            </button>
        </form>
    </div>
</header>