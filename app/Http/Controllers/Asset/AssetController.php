<?php

namespace App\Http\Controllers\Asset;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetCategory;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        $categories = AssetCategory::withCount('assets')->get();
        $employees = Employee::with('user')->where('status', 'active')->get();

        $query = Asset::with(['category', 'currentAssignment.employee.user']);
        if ($request->filled('category_id')) {
            $query->where('asset_category_id', $request->category_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assets = $query->latest()->paginate(15);

        return view('assets.index', compact('assets', 'categories', 'employees'));
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:asset_categories,code',
        ]);

        AssetCategory::create($validated);

        return redirect()->route('assets.index')->with('success', 'Asset category created.');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_category_id' => 'required|exists:asset_categories,id',
            'name' => 'required|string|max:100',
            'asset_tag' => 'required|string|max:50|unique:assets,asset_tag',
            'serial_number' => 'nullable|string|max:100|unique:assets,serial_number',
            'model' => 'nullable|string|max:100',
            'cost' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
        ]);

        Asset::create($validated);

        return redirect()->route('assets.index')->with('success', 'Company asset registered in inventory.');
    }

    public function assign(Request $request, Asset $asset): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'assigned_at' => 'required|date',
            'condition_on_assign' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['asset_id'] = $asset->id;
        AssetAssignment::create($validated);

        $asset->update(['status' => 'assigned']);

        return redirect()->route('assets.index')->with('success', "Asset {$asset->asset_tag} assigned to employee.");
    }

    public function returnAsset(Request $request, Asset $asset): RedirectResponse
    {
        $validated = $request->validate([
            'returned_at' => 'required|date',
            'condition_on_return' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $assignment = $asset->currentAssignment;
        if ($assignment) {
            $assignment->update($validated);
        }

        $asset->update(['status' => 'available']);

        return redirect()->route('assets.index')->with('success', "Asset {$asset->asset_tag} returned to inventory.");
    }
}
