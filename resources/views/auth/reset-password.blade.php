<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h1 class="text-2xl font-bold mb-6 text-center">Reset Password</h1>
            <form method="POST" action="{{ route('auth.reset-password') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ $email ?? old('email') }}" class="w-full px-3 py-2 border rounded @error('email') border-red-500 @enderror" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">New Password</label>
                    <input type="password" name="password" class="w-full px-3 py-2 border rounded @error('password') border-red-500 @enderror" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="w-full px-3 py-2 border rounded" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Reset Password</button>
            </form>
        </div>
    </div>
</x-guest-layout>