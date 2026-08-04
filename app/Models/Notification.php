<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Notification extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'type',
        'title',
        'message',
        'is_read',
        'action_url',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
