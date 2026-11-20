@extends('layouts.app')

@section('title', 'System Settings & White-Labeling')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Enterprise System Settings</h1>
            <p class="text-sm text-gray-500 mt-1">Configure company identity, branding, base currencies, and official holiday schedules.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('settings.roles.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition flex items-center space-x-2">
                <i class="fas fa-shield-alt"></i>
                <span>Role-Permission Matrix</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Company Profile & Branding Form -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-3 border-b border-gray-100 pb-4 mb-6">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Company Identity & White-Labeling</h2>
                    <p class="text-xs text-gray-500">Customize portal name, brand logo, and localization</p>
                </div>
            </div>

            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Company Legal Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? 'Nexus Enterprises') }}" class="w-full px-3.5 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Official Work Email</label>
                        <input type="email" name="company_email" value="{{ old('company_email', $settings['company_email'] ?? 'hr@nexus.com') }}" class="w-full px-3.5 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Contact Phone</label>
                        <input type="text" name="company_phone" value="{{ old('company_phone', $settings['company_phone'] ?? '+1 (555) 019-2834') }}" class="w-full px-3.5 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Corporate Website</label>
                        <input type="url" name="company_website" value="{{ old('company_website', $settings['company_website'] ?? 'https://nexus.com') }}" class="w-full px-3.5 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Headquarters Address</label>
                    <input type="text" name="company_address" value="{{ old('company_address', $settings['company_address'] ?? '100 Silicon Boulevard, Suite 500, Tech City') }}" class="w-full px-3.5 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Currency Code</label>
                        <input type="text" name="currency_code" value="{{ old('currency_code', $settings['currency_code'] ?? 'USD') }}" class="w-full px-3.5 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Currency Symbol</label>
                        <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '$') }}" class="w-full px-3.5 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Timezone</label>
                        <input type="text" name="timezone" value="{{ old('timezone', $settings['timezone'] ?? 'UTC') }}" class="w-full px-3.5 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Date Format</label>
                        <input type="text" name="date_format" value="{{ old('date_format', $settings['date_format'] ?? 'Y-m-d') }}" class="w-full px-3.5 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Company Logo (PNG/SVG)</label>
                        <input type="file" name="logo" class="w-full px-3 py-1.5 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                        Save System Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Official Holidays Schedule -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Company Holidays</h2>
                        <p class="text-xs text-gray-500">Official observed calendar</p>
                    </div>
                    <span class="px-2 py-1 rounded bg-indigo-50 text-indigo-700 text-xs font-bold">{{ $holidays->count() }} Days</span>
                </div>

                <!-- Add Holiday Form -->
                <form action="{{ route('settings.holidays.store') }}" method="POST" class="mb-6 space-y-3 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-semibold uppercase text-gray-600 mb-1">Holiday Name</label>
                        <input type="text" name="name" required placeholder="e.g. New Year's Day" class="w-full px-3 py-1.5 border rounded text-xs">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold uppercase text-gray-600 mb-1">Observed Date</label>
                        <input type="date" name="date" required class="w-full px-3 py-1.5 border rounded text-xs">
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center space-x-1.5 text-xs text-gray-600">
                            <input type="checkbox" name="is_recurring" value="1" class="rounded text-indigo-600">
                            <span>Repeats yearly</span>
                        </label>
                        <button type="submit" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded">
                            Add Holiday
                        </button>
                    </div>
                </form>

                <!-- Holiday List -->
                <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                    @forelse($holidays as $holiday)
                        <div class="flex items-center justify-between p-2.5 rounded-lg border border-gray-100 hover:bg-gray-50 text-xs">
                            <div>
                                <span class="font-bold text-gray-800">{{ $holiday->name }}</span>
                                <div class="text-gray-500 text-[11px]">{{ $holiday->date->format('M d, Y') }} @if($holiday->is_recurring) &bull; (Annual) @endif</div>
                            </div>
                            <form action="{{ route('settings.holidays.destroy', $holiday) }}" method="POST" onsubmit="return confirm('Remove holiday?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400 text-xs">
                            No holidays configured yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
