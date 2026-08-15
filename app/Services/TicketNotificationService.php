<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Support\Facades\Log;

class TicketNotificationService
{
    /**
     * Notify Superadmin when a new ticket is created by a tenant.
     */
    public static function notifySuperadminNewTicket(SupportTicket $ticket): void
    {
        try {
            Notification::withoutGlobalScopes()->create([
                'tenant_id' => null,
                'type' => 'info',
                'title' => 'New Ticket #' . $ticket->ticket_number,
                'message' => '[' . ($ticket->tenant?->name ?? 'Tenant') . '] ' . $ticket->subject,
                'is_read' => false,
                'action_url' => route('saas.tickets.show', $ticket->id),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to send superadmin new ticket notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify Superadmin when a tenant replies to a ticket.
     */
    public static function notifySuperadminTicketReply(SupportTicket $ticket, SupportTicketMessage $message): void
    {
        try {
            Notification::withoutGlobalScopes()->create([
                'tenant_id' => null,
                'type' => 'info',
                'title' => 'Reply on Ticket #' . $ticket->ticket_number,
                'message' => $message->sender_name . ' replied: ' . \Illuminate\Support\Str::limit($message->message, 80),
                'is_read' => false,
                'action_url' => route('saas.tickets.show', $ticket->id),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to send superadmin ticket reply notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify Tenant when Superadmin sends an official reply.
     */
    public static function notifyTenantTicketReply(SupportTicket $ticket, SupportTicketMessage $message): void
    {
        try {
            Notification::withoutGlobalScopes()->create([
                'tenant_id' => $ticket->tenant_id,
                'type' => 'info',
                'title' => 'New Reply: Ticket #' . $ticket->ticket_number,
                'message' => 'Support staff replied to your ticket: ' . $ticket->subject,
                'is_read' => false,
                'action_url' => route('dashboard.tickets.show', $ticket->id),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to send tenant ticket reply notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify Tenant when Superadmin updates status.
     */
    public static function notifyTenantStatusChanged(SupportTicket $ticket): void
    {
        try {
            Notification::withoutGlobalScopes()->create([
                'tenant_id' => $ticket->tenant_id,
                'type' => in_array($ticket->status, ['resolved', 'closed']) ? 'success' : 'warning',
                'title' => 'Ticket #' . $ticket->ticket_number . ' Status: ' . $ticket->status_label,
                'message' => 'Your ticket status is now ' . $ticket->status_label . '.',
                'is_read' => false,
                'action_url' => route('dashboard.tickets.show', $ticket->id),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to send tenant status update notification: ' . $e->getMessage());
        }
    }
}
