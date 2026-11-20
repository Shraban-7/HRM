@extends('layouts.app')

@section('title', 'Digital Documents & Company Policies')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Digital Documents & Policies</h1>
            <p class="text-sm text-gray-500 mt-1">Employee dossiers, ID verifications, employment contracts, and corporate policy distribution.</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="document.getElementById('policyModal').classList.remove('hidden')" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm rounded-lg transition">
                + Publish Policy
            </button>
            <button onclick="document.getElementById('docModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition flex items-center space-x-2">
                <i class="fas fa-file-upload"></i>
                <span>Upload Document</span>
            </button>
        </div>
    </div>

    <!-- Company Policies Banner -->
    <div class="bg-gradient-to-r from-indigo-900 to-gray-900 rounded-xl p-6 text-white shadow-md">
        <h2 class="text-lg font-bold flex items-center space-x-2 mb-2">
            <i class="fas fa-book-open text-indigo-400"></i>
            <span>Active Company Policies & Handbooks</span>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            @forelse($policies as $policy)
                <div class="bg-gray-800/80 p-4 rounded-lg border border-gray-700 flex flex-col justify-between">
                    <div>
                        <div class="font-bold text-sm text-white">{{ $policy->title }}</div>
                        <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $policy->description ?? 'Official company guidelines.' }}</p>
                    </div>
                    <div class="mt-4 flex items-center justify-between text-xs pt-3 border-t border-gray-700">
                        <span class="text-indigo-300 font-semibold">{{ $policy->acknowledgements_count }} Signed</span>
                        <form action="{{ route('documents.policies.acknowledge', $policy) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-500 text-white rounded text-[11px] font-bold">
                                Sign Policy
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="md:col-span-3 text-gray-400 text-xs">No official company policies published yet.</div>
            @endforelse
        </div>
    </div>

    <!-- Employee Documents Ledger -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/50">
            <h2 class="text-base font-bold text-gray-800">Employee Documents & Dossiers</h2>
            <span class="text-xs text-gray-500">{{ $documents->total() }} files secured</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3">Document Title</th>
                        <th class="px-6 py-3">Employee</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Expiry Date</th>
                        <th class="px-6 py-3 text-right">Download</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($documents as $doc)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 flex items-center space-x-2">
                                    <i class="far fa-file-pdf text-red-500 text-base"></i>
                                    <span>{{ $doc->title }}</span>
                                </div>
                                <div class="text-[11px] text-gray-400 mt-0.5">{{ number_format($doc->file_size / 1024, 1) }} KB &bull; Uploaded {{ $doc->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-gray-800">
                                {{ $doc->employee->user->username ?? 'Employee #'.$doc->employee_id }}
                                <div class="text-[11px] text-gray-400 font-normal">{{ $doc->employee->employee_id }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-medium">{{ $doc->category->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                @if($doc->expires_at)
                                    <span class="{{ $doc->expires_at->isPast() ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                        {{ $doc->expires_at->format('M d, Y') }}
                                        @if($doc->expires_at->isPast()) (Expired) @endif
                                    </span>
                                @else
                                    <span class="text-gray-400">No Expiry</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('documents.download', $doc) }}" class="inline-flex items-center space-x-1 text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition">
                                    <i class="fas fa-download"></i>
                                    <span>Download</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">No employee documents uploaded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $documents->links() }}
        </div>
    </div>
</div>

<!-- Upload Document Modal -->
<div id="docModal" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Upload Employee Document</h3>
        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Employee</label>
                <select name="employee_id" class="w-full px-3 py-2 border rounded-lg text-sm" required>
                    <option value="">Select Employee...</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->user->username ?? 'ID #'.$emp->id }} ({{ $emp->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Document Category</label>
                <select name="document_category_id" class="w-full px-3 py-2 border rounded-lg text-sm" required>
                    @forelse($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @empty
                        <option value="">(Create category first)</option>
                    @endforelse
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Document Title</label>
                <input type="text" name="title" required placeholder="Passport Copy / Employment Contract" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Expiration Date (Optional)</label>
                <input type="date" name="expires_at" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">File (PDF, DOC, Images - Max 5MB)</label>
                <input type="file" name="file" required class="w-full px-3 py-1.5 border rounded-lg text-sm">
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('docModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700">Upload</button>
            </div>
        </form>
    </div>
</div>

<!-- Publish Policy Modal -->
<div id="policyModal" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Publish Company Policy</h3>
        <form action="{{ route('documents.policies.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Policy Title</label>
                <input type="text" name="title" required placeholder="Remote Work & Cyber Security Policy" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Effective Date</label>
                <input type="date" name="published_at" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Brief Description</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">PDF Attachment (Optional)</label>
                <input type="file" name="file" accept=".pdf" class="w-full px-3 py-1.5 border rounded-lg text-sm">
            </div>
            <label class="flex items-center space-x-2 text-xs text-gray-700">
                <input type="checkbox" name="requires_acknowledgement" value="1" checked class="rounded text-indigo-600">
                <span>Requires employee signature acknowledgement</span>
            </label>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('policyModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700">Publish Policy</button>
            </div>
        </form>
    </div>
</div>
@endsection
