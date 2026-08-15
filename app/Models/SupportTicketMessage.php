<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_ticket_id',
        'sender_type',
        'sender_id',
        'message',
        'attachments',
        'is_internal_note',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal_note' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    public function getSenderAttribute()
    {
        if ($this->sender_type === 'admin') {
            return Admin::find($this->sender_id);
        }
        return User::find($this->sender_id);
    }

    public function getSenderNameAttribute(): string
    {
        if ($this->sender_type === 'admin') {
            return $this->sender?->name ?? 'Support Staff';
        }
        return $this->sender?->name ?? 'Customer';
    }

    public function isFromAdmin(): bool
    {
        return $this->sender_type === 'admin';
    }

    public function isFromUser(): bool
    {
        return $this->sender_type === 'user';
    }
}
