<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
            <div class="text-center mb-6">
                <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center mx-auto text-white text-xl shadow-lg shadow-indigo-500/30 mb-3">
                    <i class="fas fa-users-viewfinder"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900">NexusHRM Portal</h1>
                <p class="text-xs text-gray-500 mt-1">Sign in with your enterprise credentials</p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-xs px-3 py-2 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 text-xs px-3 py-2 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('auth.login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Email or Username</label>
                    <input type="text" name="identifier" value="{{ old('identifier') }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('identifier') border-red-500 @enderror" required autofocus>
                    @error('identifier') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold uppercase text-gray-700">Password</label>
                        <a href="{{ route('auth.forgot-password') }}" class="text-xs text-indigo-600 hover:underline">Forgot?</a>
                    </div>
                    <input type="password" name="password" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror" required>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="remember" class="ml-2 block text-xs text-gray-600">Keep me signed in on this device</label>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white font-semibold py-2.5 px-4 rounded-xl hover:bg-indigo-700 shadow-md shadow-indigo-600/20 transition">Sign In</button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-100">
                <div class="text-center text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">1-Click Demo Evaluation</div>
                <div class="grid grid-cols-2 gap-2">
                    <form action="{{ route('demo.switch', 'super_admin') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-1.5 px-2 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-semibold text-xs border border-indigo-200 transition flex items-center justify-center space-x-1">
                            <i class="fas fa-shield-alt text-[10px]"></i>
                            <span>Super Admin</span>
                        </button>
                    </form>
                    <form action="{{ route('demo.switch', 'hr_admin') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-1.5 px-2 rounded-lg bg-teal-50 text-teal-700 hover:bg-teal-100 font-semibold text-xs border border-teal-200 transition flex items-center justify-center space-x-1">
                            <i class="fas fa-user-tie text-[10px]"></i>
                            <span>HR Manager</span>
                        </button>
                    </form>
                    <form action="{{ route('demo.switch', 'manager') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-1.5 px-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 font-semibold text-xs border border-blue-200 transition flex items-center justify-center space-x-1">
                            <i class="fas fa-briefcase text-[10px]"></i>
                            <span>Dept Manager</span>
                        </button>
                    </form>
                    <form action="{{ route('demo.switch', 'employee') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-1.5 px-2 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-semibold text-xs border border-emerald-200 transition flex items-center justify-center space-x-1">
                            <i class="fas fa-user text-[10px]"></i>
                            <span>Employee</span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-6 text-center text-xs text-gray-500">
                <a href="/" class="text-gray-500 hover:text-indigo-600 transition flex items-center justify-center space-x-1">
                    <i class="fas fa-arrow-left text-[10px]"></i>
                    <span>Back to NexusHRM Overview</span>
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>