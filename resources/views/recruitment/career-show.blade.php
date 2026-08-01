<x-guest-layout>
    <div class="min-h-screen bg-gray-100 py-12">
        <div class="max-w-3xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow p-6">
                <a href="{{ route('careers.index') }}" class="text-blue-600 hover:underline">&larr; Back to all jobs</a>
                <h1 class="text-2xl font-bold text-gray-900 mt-4">{{ $job->title }}</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Closing date: {{ $job->closing_date ? \Illuminate\Support\Carbon::parse($job->closing_date)->toFormattedDateString() : 'Open until filled' }}
                </p>

                <div class="mt-6 prose max-w-none">
                    <h2 class="text-lg font-semibold">Job Description</h2>
                    <div class="whitespace-pre-wrap text-gray-700">{!! nl2br(e($job->description)) !!}</div>
                </div>

                @if($job->requirements)
                    <div class="mt-6">
                        <h2 class="text-lg font-semibold">Requirements</h2>
                        <div class="whitespace-pre-wrap text-gray-700">{!! nl2br(e($job->requirements)) !!}</div>
                    </div>
                @endif

                <div class="mt-8 border-t pt-6">
                    <h2 class="text-lg font-semibold mb-4">Apply for this position</h2>
                    <form method="POST" action="{{ route('careers.apply', $job) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 mb-1">Full Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="w-full px-3 py-2 border rounded" required>
                                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 border rounded" required>
                                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-1">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-3 py-2 border rounded">
                            </div>
                            <div>
                                <label class="block text-gray-700 mb-1">Resume (PDF/DOC)</label>
                                <input type="file" name="resume" accept=".pdf,.doc,.docx" class="w-full px-3 py-2 border rounded" required>
                                @error('resume') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <button type="submit" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Submit Application</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
