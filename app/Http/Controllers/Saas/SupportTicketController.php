<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\Tenant;
use App\Services\TicketNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupportTicketController extends Controller
{
    public function index(Request $request): View
    {
        $query = SupportTicket::withoutGlobalScopes()
            ->with(['tenant', 'user', 'assignedAdmin', 'latestMessage']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('tenant', function ($t) use ($search) {
                      $t->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Tenant Filter
        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
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

        // Assigned Admin Filter
        if ($request->filled('assigned_to')) {
            if ($request->assigned_to === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        // Category Filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Summary KPI Stats
        $stats = [
            'total' => SupportTicket::withoutGlobalScopes()->count(),
            'open' => SupportTicket::withoutGlobalScopes()->whereIn('status', ['open', 'in_progress', 'tenant_reply'])->count(),
            'pending_admin' => SupportTicket::withoutGlobalScopes()->whereIn('status', ['open', 'tenant_reply'])->count(),
            'resolved' => SupportTicket::withoutGlobalScopes()->whereIn('status', ['resolved', 'closed'])->count(),
        ];

        $tickets = $query->orderBy('last_reply_at', 'desc')->paginate(15)->withQueryString();
        $tenants = Tenant::orderBy('name')->get();
        $admins = Admin::orderBy('name')->get();

        return view('admin.saas.support.index', compact('tickets', 'stats', 'tenants', 'admins'));
    }

    public function show($id): View
    {
        $ticket = SupportTicket::withoutGlobalScopes()
            ->with([
                'tenant.subscriptions.plan',
                'user',
                'assignedAdmin',
                'messages' => function ($q) {
                    $q->orderBy('created_at', 'asc');
                }
            ])
            ->findOrFail($id);

        $admins = Admin::orderBy('name')->get();

        return view('admin.saas.support.show', compact('ticket', 'admins'));
    }

    public function reply(Request $request, $id): RedirectResponse
    {
        $ticket = SupportTicket::withoutGlobalScopes()->findOrFail($id);

        $validated = $request->validate([
            'message' => 'required|string',
            'is_internal_note' => 'nullable|boolean',
            'status' => 'nullable|in:open,in_progress,answered,resolved,closed',
            'attachments.*' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,pdf,doc,docx,zip,txt|max:10240',
        ]);

        $isInternal = $request->boolean('is_internal_note');
        $attachmentsData = $this->handleUploads($request, $ticket->id);

        $message = SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'sender_type' => 'admin',
            'sender_id' => auth('admin')->id() ?? 1,
            'message' => $validated['message'],
            'attachments' => !empty($attachmentsData) ? $attachmentsData : null,
            'is_internal_note' => $isInternal,
        ]);

        if (!$isInternal) {
            $newStatus = $request->input('status', 'answered');
            $ticket->update([
                'status' => $newStatus,
                'last_reply_at' => now(),
                'closed_at' => in_array($newStatus, ['resolved', 'closed']) ? now() : null,
            ]);

            TicketNotificationService::notifyTenantTicketReply($ticket, $message);
        }

        $successMsg = $isInternal ? 'Internal staff note added successfully.' : 'Reply sent to tenant customer.';
        return back()->with('success', $successMsg);
    }

    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $ticket = SupportTicket::withoutGlobalScopes()->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,answered,tenant_reply,resolved,closed',
        ]);

        $oldStatus = $ticket->status;
        $newStatus = $validated['status'];

        $ticket->update([
            'status' => $newStatus,
            'closed_at' => in_array($newStatus, ['resolved', 'closed']) ? now() : null,
        ]);

        if ($oldStatus !== $newStatus) {
            TicketNotificationService::notifyTenantStatusChanged($ticket);
        }

        return back()->with('success', 'Ticket status updated to ' . $ticket->status_label . '.');
    }

    public function updatePriority(Request $request, $id): RedirectResponse
    {
        $ticket = SupportTicket::withoutGlobalScopes()->findOrFail($id);

        $validated = $request->validate([
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $ticket->update(['priority' => $validated['priority']]);

        return back()->with('success', 'Ticket priority updated to ' . ucfirst($validated['priority']) . '.');
    }

    public function assign(Request $request, $id): RedirectResponse
    {
        $ticket = SupportTicket::withoutGlobalScopes()->findOrFail($id);

        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:admins,id',
        ]);

        $ticket->update(['assigned_to' => $validated['assigned_to']]);

        return back()->with('success', 'Ticket staff assignment updated.');
    }

    public function destroy($id): RedirectResponse
    {
        $ticket = SupportTicket::withoutGlobalScopes()->findOrFail($id);
        
        // Delete storage folder if exists
        Storage::disk('public')->deleteDirectory("tickets/{$ticket->id}");

        $ticket->delete();

        return redirect()->route('saas.tickets.index')->with('success', 'Ticket deleted successfully.');
    }

    public function downloadAttachment($ticketId, $messageId, int $index): StreamedResponse
    {
        $ticket = SupportTicket::withoutGlobalScopes()->findOrFail($ticketId);
        $message = SupportTicketMessage::where('support_ticket_id', $ticket->id)->findOrFail($messageId);

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
