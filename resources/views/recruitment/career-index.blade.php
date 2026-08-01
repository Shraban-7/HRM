<x-guest-layout>
    <div class="min-h-screen bg-gray-100 py-12">
        <div class="max-w-4xl mx-auto px-4">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-gray-900">Careers at Our Company</h1>
                <p class="mt-2 text-gray-600">Explore open positions and join our team.</p>
            </div>

            <div class="space-y-4">
                @forelse($jobs as $job)
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">{{ $job->title }}</h2>
                                <p class="mt-1 text-sm text-gray-500">
                                    Closing date: {{ $job->closing_date ? \Illuminate\Support\Carbon::parse($job->closing_date)->toFormattedDateString() : 'Open until filled' }}
                                </p>
                            </div>
                            <a href="{{ route('careers.show', $job) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">View & Apply</a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow p-10 text-center text-gray-500">
                        No open positions at the moment. Please check back later.
                    </div>
                @endforelse
            </div>

            <div class="mt-10 text-center">
                <a href="{{ route('auth.login') }}" class="text-blue-600 hover:underline">Employee login</a>
            </div>
        </div>
    </div>
</x-guest-layout>
