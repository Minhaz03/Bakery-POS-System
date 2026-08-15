<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\BelongsToTenant;
use Illuminate\Support\Str;

class SupportTicket extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'ticket_number',
        'tenant_id',
        'user_id',
        'assigned_to',
        'subject',
        'category',
        'priority',
        'status',
        'last_reply_at',
        'closed_at',
    ];

    protected $casts = [
        'last_reply_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TK-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            }
            if (empty($ticket->last_reply_at)) {
                $ticket->last_reply_at = now();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class);
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(SupportTicketMessage::class)->latestOfMany();
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'tenant_reply']);
    }

    public function scopeClosed($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    public function scopePendingAdmin($query)
    {
        return $query->whereIn('status', ['open', 'tenant_reply']);
    }

    // Helpers
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'answered' => 'Staff Answered',
            'tenant_reply' => 'Customer Reply',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'open' => ['bg' => 'rgba(239, 68, 68, 0.12)', 'color' => '#dc2626', 'icon' => 'bi-envelope-open'],
            'in_progress' => ['bg' => 'rgba(245, 158, 11, 0.12)', 'color' => '#d97706', 'icon' => 'bi-hourglass-split'],
            'answered' => ['bg' => 'rgba(99, 102, 241, 0.12)', 'color' => '#4f46e5', 'icon' => 'bi-reply-all'],
            'tenant_reply' => ['bg' => 'rgba(14, 165, 233, 0.12)', 'color' => '#0284c7', 'icon' => 'bi-chat-left-dots'],
            'resolved' => ['bg' => 'rgba(16, 185, 129, 0.12)', 'color' => '#059669', 'icon' => 'bi-check-circle'],
            'closed' => ['bg' => 'rgba(100, 116, 139, 0.12)', 'color' => '#475569', 'icon' => 'bi-archive'],
            default => ['bg' => 'rgba(100, 116, 139, 0.12)', 'color' => '#475569', 'icon' => 'bi-circle'],
        };
    }

    public function getPriorityBadgeAttribute(): array
    {
        return match ($this->priority) {
            'urgent' => ['bg' => 'rgba(220, 38, 38, 0.12)', 'color' => '#b91c1c', 'label' => 'Urgent', 'icon' => 'bi-exclamation-octagon-fill'],
            'high' => ['bg' => 'rgba(239, 68, 68, 0.12)', 'color' => '#dc2626', 'label' => 'High', 'icon' => 'bi-exclamation-triangle-fill'],
            'medium' => ['bg' => 'rgba(245, 158, 11, 0.12)', 'color' => '#d97706', 'label' => 'Medium', 'icon' => 'bi-dash-circle-fill'],
            'low' => ['bg' => 'rgba(16, 185, 129, 0.12)', 'color' => '#059669', 'label' => 'Low', 'icon' => 'bi-arrow-down-circle-fill'],
            default => ['bg' => 'rgba(100, 116, 139, 0.12)', 'color' => '#64748b', 'label' => ucfirst($this->priority), 'icon' => 'bi-circle'],
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'technical' => 'Technical Support',
            'billing' => 'Billing & Payment',
            'feature_request' => 'Feature Request',
            'bug_report' => 'Bug Report',
            'account' => 'Account / Tenant',
            default => 'General Inquiry',
        };
    }

    public function isClosed(): bool
    {
        return in_array($this->status, ['closed', 'resolved']);
    }
}
