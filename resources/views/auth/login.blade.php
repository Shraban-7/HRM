<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h1 class="text-2xl font-bold mb-6 text-center">HRM System Login</h1>
            <form method="POST" action="{{ route('auth.login') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Email or Username</label>
                    <input type="text" name="identifier" value="{{ old('identifier') }}" class="w-full px-3 py-2 border rounded @error('identifier') border-red-500 @enderror" required>
                    @error('identifier') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" class="w-full px-3 py-2 border rounded @error('password') border-red-500 @enderror" required>
                </div>
                <div class="mb-4 flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="mr-2">
                    <label for="remember">Remember me</label>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Login</button>
            </form>
            <div class="mt-4 text-center space-y-2">
                <a href="{{ route('auth.register') }}" class="text-blue-600 hover:underline block">Register</a>
                <a href="{{ route('auth.forgot-password') }}" class="text-blue-600 hover:underline block">Forgot Password?</a>
            </div>
        </div>
    </div>
</x-guest-layout>