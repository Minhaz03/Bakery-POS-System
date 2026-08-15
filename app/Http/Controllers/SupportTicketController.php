<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Services\TicketNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupportTicketController extends Controller
{
    public function index(Request $request): View
    {
        $query = SupportTicket::with(['user', 'latestMessage']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            if ($request->status === 'open') {
                $query->whereIn('status', ['open', 'in_progress', 'tenant_reply']);
            } elseif ($request->status === 'closed') {
                $query->whereIn('status', ['resolved', 'closed']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Priority Filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Category Filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Stats for cards
        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::whereIn('status', ['open', 'in_progress', 'tenant_reply'])->count(),
            'answered' => SupportTicket::where('status', 'answered')->count(),
            'closed' => SupportTicket::whereIn('status', ['resolved', 'closed'])->count(),
        ];

        $tickets = $query->orderBy('last_reply_at', 'desc')->paginate(15)->withQueryString();

        return view('dashboard.support.index', compact('tickets', 'stats'));
    }

    public function create(): View
    {
        return view('dashboard.support.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|in:general,technical,billing,feature_request,bug_report,account',
            'priority' => 'required|in:low,medium,high,urgent',
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,pdf,doc,docx,zip,txt|max:10240',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => 'open',
            'last_reply_at' => now(),
        ]);

        $attachmentsData = $this->handleUploads($request, $ticket->id);

        $message = SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_type' => 'user',
            'sender_id' => auth()->id(),
            'message' => $validated['message'],
            'attachments' => !empty($attachmentsData) ? $attachmentsData : null,
            'is_internal_note' => false,
        ]);

        TicketNotificationService::notifySuperadminNewTicket($ticket);

        return redirect()->route('dashboard.tickets.show', $ticket)
            ->with('success', 'Support ticket #' . $ticket->ticket_number . ' created successfully.');
    }

    public function show(SupportTicket $ticket): View
    {
        // Enforce tenant boundary
        if ($ticket->tenant_id !== (auth()->user()->tenant_id ?? 1)) {
            abort(403, 'Unauthorized access to this ticket.');
        }

        $ticket->load([
            'user',
            'assignedAdmin',
            'messages' => function ($q) {
                $q->where('is_internal_note', false)->orderBy('created_at', 'asc');
            }
        ]);

        return view('dashboard.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        if ($ticket->tenant_id !== (auth()->user()->tenant_id ?? 1)) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,pdf,doc,docx,zip,txt|max:10240',
        ]);

        $attachmentsData = $this->handleUploads($request, $ticket->id);

        $message = SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_type' => 'user',
            'sender_id' => auth()->id(),
            'message' => $validated['message'],
            'attachments' => !empty($attachmentsData) ? $attachmentsData : null,
            'is_internal_note' => false,
        ]);

        $ticket->update([
            'status' => 'tenant_reply',
            'last_reply_at' => now(),
            'closed_at' => null,
        ]);

        TicketNotificationService::notifySuperadminTicketReply($ticket, $message);

        return back()->with('success', 'Your reply has been sent.');
    }

    public function close(SupportTicket $ticket): RedirectResponse
    {
        if ($ticket->tenant_id !== (auth()->user()->tenant_id ?? 1)) {
            abort(403, 'Unauthorized action.');
        }

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return back()->with('success', 'Ticket marked as closed.');
    }

    public function reopen(SupportTicket $ticket): RedirectResponse
    {
        if ($ticket->tenant_id !== (auth()->user()->tenant_id ?? 1)) {
            abort(403, 'Unauthorized action.');
        }

        $ticket->update([
            'status' => 'open',
            'closed_at' => null,
            'last_reply_at' => now(),
        ]);

        return back()->with('success', 'Ticket reopened.');
    }

    public function downloadAttachment(SupportTicket $ticket, SupportTicketMessage $message, int $index): StreamedResponse
    {
        if ($ticket->tenant_id !== (auth()->user()->tenant_id ?? 1) || $message->support_ticket_id !== $ticket->id) {
            abort(403, 'Unauthorized attachment access.');
        }

        $attachments = $message->attachments ?? [];
        if (!isset($attachments[$index])) {
            abort(404, 'Attachment not found.');
        }

        $file = $attachments[$index];
        if (!Storage::disk('public')->exists($file['path'])) {
            abort(404, 'File missing from storage.');
        }

        return Storage::disk('public')->download($file['path'], $file['name']);
    }

    private function handleUploads(Request $request, int $ticketId): array
    {
        $uploaded = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $originalName = $file->getClientOriginalName();
                $path = $file->store("tickets/{$ticketId}", 'public');
                $uploaded[] = [
                    'name' => $originalName,
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                ];
            }
        }
        return $uploaded;
    }
}
