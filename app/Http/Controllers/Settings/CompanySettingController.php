<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\Holiday;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanySettingController extends Controller
{
    public function index(): View
    {
        $settings = CompanySetting::all()->pluck('value', 'key');
        $holidays = Holiday::orderBy('date')->get();

        return view('settings.index', compact('settings', 'holidays'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:100',
            'company_email' => 'nullable|email|max:100',
            'company_phone' => 'nullable|string|max:30',
            'company_address' => 'nullable|string|max:255',
            'company_website' => 'nullable|url|max:100',
            'currency_code' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:5',
            'timezone' => 'required|string|max:50',
            'date_format' => 'required|string|max:20',
            'fiscal_year_start' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('branding', 'public');
            CompanySetting::set('company_logo_path', $path, 'branding');
        }

        foreach ($data as $key => $val) {
            if ($key !== 'logo') {
                CompanySetting::set($key, (string) $val, 'general');
            }
        }

        return redirect()->route('settings.index')->with('success', 'Company profile and system settings updated successfully.');
    }

    public function storeHoliday(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'is_recurring' => 'nullable|boolean',
        ]);

        $validated['is_recurring'] = $request->has('is_recurring');
        Holiday::create($validated);

        return redirect()->route('settings.index')->with('success', 'Public holiday added to company calendar.');
    }

    public function destroyHoliday(Holiday $holiday): RedirectResponse
    {
        $holiday->delete();

        return redirect()->route('settings.index')->with('success', 'Holiday removed successfully.');
    }
}
