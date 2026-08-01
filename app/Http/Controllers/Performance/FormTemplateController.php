<?php

namespace App\Http\Controllers\Performance;

use App\Http\Controllers\Controller;
use App\Models\PerformanceFormTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormTemplateController extends Controller
{
    public function index()
    {
        $templates = PerformanceFormTemplate::withCount('reviews')->orderBy('created_at', 'desc')->paginate(15);

        return view('performance.templates', compact('templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'questions' => 'required|array|min:1',
            'questions.*' => 'required|string|max:500',
        ]);

        PerformanceFormTemplate::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']).'-'.Str::random(4),
            'description' => $validated['description'] ?? null,
            'questions' => array_values($validated['questions']),
            'is_active' => true,
        ]);

        return redirect()->route('performance.templates.index')
            ->with('success', 'Form template created.');
    }

    public function update(Request $request, PerformanceFormTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'questions' => 'required|array|min:1',
            'questions.*' => 'required|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $template->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'questions' => array_values($validated['questions']),
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return redirect()->route('performance.templates.index')
            ->with('success', 'Form template updated.');
    }

    public function destroy(PerformanceFormTemplate $template)
    {
        $template->delete();

        return redirect()->route('performance.templates.index')
            ->with('success', 'Form template deleted.');
    }
}
