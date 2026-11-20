@extends('layouts.app')

@section('title', 'Company Assets & Equipment')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Company Asset Inventory</h1>
            <p class="text-sm text-gray-500 mt-1">Track hardware allocations, laptops, monitors, accessories, and warranty lifecycles.</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="document.getElementById('categoryModal').classList.remove('hidden')" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm rounded-lg transition">
                + New Category
            </button>
            <button onclick="document.getElementById('assetModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Register Asset</span>
            </button>
        </div>
    </div>

    <!-- Category Pills -->
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('assets.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ !request('category_id') ? 'bg-indigo-600 text-white' : 'bg-white border text-gray-700 hover:bg-gray-50' }}">
            All Categories ({{ $assets->total() }})
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('assets.index', ['category_id' => $cat->id]) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('category_id') == $cat->id ? 'bg-indigo-600 text-white' : 'bg-white border text-gray-700 hover:bg-gray-50' }}">
                {{ $cat->name }} ({{ $cat->assets_count }})
            </a>
        @endforeach
    </div>

    <!-- Assets Inventory Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3">Asset Tag / Name</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Serial # / Model</th>
                        <th class="px-6 py-3">Assigned Employee</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 flex items-center space-x-2">
                                    <span class="font-mono text-xs bg-gray-100 text-gray-800 px-2 py-0.5 rounded">{{ $asset->asset_tag }}</span>
                                    <span>{{ $asset->name }}</span>
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">Purchased: {{ $asset->purchase_date ? $asset->purchase_date->format('M Y') : 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-gray-600">
                                {{ $asset->category->name }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                <div>{{ $asset->model ?? '-' }}</div>
                                <div class="font-mono text-[11px] text-gray-400">{{ $asset->serial_number ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                @if($asset->currentAssignment)
                                    <div class="font-semibold text-gray-800">{{ $asset->currentAssignment->employee->user->username ?? 'Employee #'.$asset->currentAssignment->employee_id }}</div>
                                    <div class="text-[11px] text-gray-400">Assigned: {{ $asset->currentAssignment->assigned_at->format('M d, Y') }}</div>
                                @else
                                    <span class="text-gray-400 italic">In Storage / Unassigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($asset->status === 'available')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Available</span>
                                @elseif($asset->status === 'assigned')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Assigned</span>
                                @elseif($asset->status === 'maintenance')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Maintenance</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">{{ ucfirst($asset->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @if($asset->status === 'available')
                                    <button onclick="openAssignModal({{ $asset->id }}, '{{ addslashes($asset->name) }} ({{ $asset->asset_tag }})')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                        Assign
                                    </button>
                                @elseif($asset->status === 'assigned')
                                    <button onclick="openReturnModal({{ $asset->id }}, '{{ addslashes($asset->name) }} ({{ $asset->asset_tag }})')" class="text-xs font-bold text-rose-600 hover:text-rose-800">
                                        Return
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">No assets registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $assets->links() }}
        </div>
    </div>
</div>

<!-- Register Asset Modal -->
<div id="assetModal" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Register New Asset</h3>
        <form action="{{ route('assets.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Asset Category</label>
                    <select name="asset_category_id" class="w-full px-3 py-2 border rounded-lg text-sm" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Asset Tag</label>
                    <input type="text" name="asset_tag" required placeholder="e.g. LAP-2026-001" class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Asset Name</label>
                <input type="text" name="name" required placeholder="e.g. MacBook Pro 16-inch M3 Max" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Model / Specs</label>
                    <input type="text" name="model" placeholder="36GB RAM / 1TB SSD" class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Serial Number</label>
                    <input type="text" name="serial_number" placeholder="C02XG..." class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Purchase Cost ($)</label>
                    <input type="number" step="0.01" name="cost" placeholder="2499.00" class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Purchase Date</label>
                    <input type="date" name="purchase_date" class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('assetModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700">Save Asset</button>
            </div>
        </form>
    </div>
</div>

<!-- Category Modal -->
<div id="categoryModal" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Asset Category</h3>
        <form action="{{ route('assets.categories.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Category Name</label>
                <input type="text" name="name" required placeholder="Laptops, Monitors, Phones" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Code</label>
                <input type="text" name="code" required placeholder="LAP, MON, PHN" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('categoryModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Assign Modal -->
<div id="assignModal" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900 mb-1">Assign Asset</h3>
        <p id="assignModalTitle" class="text-xs text-gray-500 mb-4"></p>
        <form id="assignForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Assign To Employee</label>
                <select name="employee_id" class="w-full px-3 py-2 border rounded-lg text-sm" required>
                    <option value="">Select Employee...</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->user->username ?? 'ID #'.$emp->id }} ({{ $emp->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Assignment Date</label>
                <input type="date" name="assigned_at" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Condition on Handover</label>
                <input type="text" name="condition_on_assign" value="Brand New / Good" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('assignModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700">Assign Asset</button>
            </div>
        </form>
    </div>
</div>

<!-- Return Modal -->
<div id="returnModal" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900 mb-1">Return Asset to Inventory</h3>
        <p id="returnModalTitle" class="text-xs text-gray-500 mb-4"></p>
        <form id="returnForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Return Date</label>
                <input type="date" name="returned_at" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Condition Upon Return</label>
                <select name="condition_on_return" class="w-full px-3 py-2 border rounded-lg text-sm" required>
                    <option value="good">Good / Reusable</option>
                    <option value="damaged">Damaged / Needs Repair</option>
                    <option value="scratched">Minor Wear and Tear</option>
                    <option value="broken">Broken / Write-Off</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Remarks</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Power adapter included, etc."></textarea>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('returnModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-lg text-xs font-bold hover:bg-rose-700">Confirm Return</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAssignModal(id, title) {
    document.getElementById('assignModalTitle').innerText = title;
    document.getElementById('assignForm').action = '/assets/' + id + '/assign';
    document.getElementById('assignModal').classList.remove('hidden');
}

function openReturnModal(id, title) {
    document.getElementById('returnModalTitle').innerText = title;
    document.getElementById('returnForm').action = '/assets/' + id + '/return';
    document.getElementById('returnModal').classList.remove('hidden');
}
</script>
@endsection
