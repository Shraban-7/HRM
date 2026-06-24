<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h1 class="text-2xl font-bold mb-6 text-center">Create Account</h1>
            <form method="POST" action="{{ route('auth.register') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" class="w-full px-3 py-2 border rounded @error('username') border-red-500 @enderror" required>
                    @error('username') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 border rounded @error('email') border-red-500 @enderror" required>
                    @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" class="w-full px-3 py-2 border rounded @error('password') border-red-500 @enderror" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="w-full px-3 py-2 border rounded" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Register</button>
            </form>
            <div class="mt-4 text-center">
                <a href="{{ route('auth.login') }}" class="text-blue-600 hover:underline">Already have an account? Login</a>
            </div>
        </div>
    </div>
</x-guest-layout>