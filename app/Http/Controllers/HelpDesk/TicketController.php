<?php

namespace App\Http\Controllers\HelpDesk;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $employees = Employee::with('user')->where('status', 'active')->get();

        $query = Ticket::with(['employee.user', 'assignedAgent']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        $tickets = $query->latest()->paginate(15);

        return view('helpdesk.index', compact('tickets', 'employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'department' => 'required|string',
            'subject' => 'required|string|max:150',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $validated['ticket_number'] = 'TICK-'.strtoupper(Str::random(6));

        $ticket = Ticket::create($validated);

        return redirect()->route('helpdesk.index')->with('success', "Ticket {$ticket->ticket_number} submitted.");
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['employee.user', 'assignedAgent', 'comments.user']);

        return view('helpdesk.show', compact('ticket'));
    }

    public function storeComment(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'is_internal' => 'nullable|boolean',
        ]);

        $validated['ticket_id'] = $ticket->id;
        $validated['user_id'] = Auth::id() ?? session('user_id');
        $validated['is_internal'] = $request->has('is_internal');

        TicketComment::create($validated);

        if ($request->filled('status')) {
            $ticket->update(['status' => $request->status]);
        }

        return redirect()->route('helpdesk.show', $ticket)->with('success', 'Comment appended to ticket.');
    }
}
