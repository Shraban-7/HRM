@extends('layouts.app')

@section('title', 'Role-Permission Matrix')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dynamic Role-Based Access Control (RBAC)</h1>
            <p class="text-sm text-gray-500 mt-1">Configure security permissions across departments and roles.</p>
        </div>
        <a href="{{ route('settings.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 flex items-center space-x-1">
            <i class="fas fa-arrow-left text-xs"></i>
            <span>Back to System Settings</span>
        </a>
    </div>

    <!-- Create Custom Role Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 flex items-center space-x-2">
            <i class="fas fa-plus-circle text-indigo-600"></i>
            <span>Create Custom Enterprise Role</span>
        </h2>
        <form action="{{ route('settings.roles.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Role Title</label>
                <input type="text" name="name" required placeholder="e.g. Payroll Auditor" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Description</label>
                <input type="text" name="description" placeholder="Short description of responsibilities" class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                    Create Role
                </button>
            </div>
        </form>
    </div>

    <!-- Roles & Permissions Matrix -->
    <div class="space-y-6">
        @foreach($roles as $role)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 flex items-center space-x-2">
                            <span>{{ $role->name }}</span>
                            <span class="text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded font-mono">{{ $role->slug }}</span>
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $role->description ?? 'No description provided' }}</p>
                    </div>
                    <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200">
                        {{ $role->permissions->count() }} Permissions Active
                    </span>
                </div>

                <form action="{{ route('settings.roles.update', $role) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="name" value="{{ $role->name }}">
                    <input type="hidden" name="description" value="{{ $role->description }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($permissions as $module => $modulePerms)
                            <div class="border border-gray-100 rounded-lg p-3 bg-gray-50/50">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700 border-b border-gray-200 pb-2 mb-2 flex items-center justify-between">
                                    <span>{{ ucfirst($module) }}</span>
                                    <span class="text-[10px] text-gray-400">({{ $modulePerms->count() }})</span>
                                </h4>
                                <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                    @foreach($modulePerms as $perm)
                                        <label class="flex items-start space-x-2 text-xs text-gray-600 hover:text-gray-900 cursor-pointer">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" class="mt-0.5 rounded text-indigo-600 border-gray-300" {{ $role->permissions->contains($perm->id) ? 'checked' : '' }}>
                                            <span class="leading-tight">{{ $perm->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-end border-t border-gray-100 pt-4">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg transition shadow-sm">
                            Save Changes for {{ $role->name }}
                        </button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
