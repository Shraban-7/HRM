<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Models\CompanyPolicy;
use App\Models\DocumentCategory;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\PolicyAcknowledgement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $categories = DocumentCategory::withCount('documents')->get();
        $employees = Employee::with('user')->where('status', 'active')->get();
        $policies = CompanyPolicy::withCount('acknowledgements')->latest()->get();

        $query = EmployeeDocument::with(['employee.user', 'category']);
        if ($request->filled('category_id')) {
            $query->where('document_category_id', $request->category_id);
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $documents = $query->latest()->paginate(15);

        return view('documents.index', compact('documents', 'categories', 'employees', 'policies'));
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:document_categories,name',
        ]);

        DocumentCategory::create($validated);

        return redirect()->route('documents.index')->with('success', 'Document category created.');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'document_category_id' => 'required|exists:document_categories,id',
            'title' => 'required|string|max:150',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'expires_at' => 'nullable|date',
        ]);

        $file = $request->file('file');
        $path = $file->store('employee_documents', 'local');

        EmployeeDocument::create([
            'employee_id' => $validated['employee_id'],
            'document_category_id' => $validated['document_category_id'],
            'title' => $validated['title'],
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType(),
            'expires_at' => $validated['expires_at'] ?? null,
            'status' => 'active',
        ]);

        return redirect()->route('documents.index')->with('success', 'Document uploaded to employee dossier.');
    }

    public function download(EmployeeDocument $document): BinaryFileResponse
    {
        if (! Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'File not found on storage.');
        }

        return response()->download(Storage::disk('local')->path($document->file_path), $document->title);
    }

    public function storePolicy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'published_at' => 'required|date',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('company_policies', 'public');
        }

        $validated['requires_acknowledgement'] = $request->has('requires_acknowledgement');
        CompanyPolicy::create($validated);

        return redirect()->route('documents.index')->with('success', 'Company policy published.');
    }

    public function acknowledgePolicy(Request $request, CompanyPolicy $policy): RedirectResponse
    {
        $user = auth()->user() ?? \App\Models\User::find(session('user_id'));
        $employee = $user?->employee;

        if ($employee) {
            PolicyAcknowledgement::firstOrCreate([
                'company_policy_id' => $policy->id,
                'employee_id' => $employee->id,
            ], [
                'acknowledged_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Policy acknowledged.');
    }
}
