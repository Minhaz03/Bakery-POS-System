<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = ['tenant_id', 'plan_id', 'starts_at', 'ends_at', 'status', 'transaction_id', 'amount', 'payment_status', 'payment_method', 'gateway_response'];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'gateway_response' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
