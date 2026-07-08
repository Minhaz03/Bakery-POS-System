<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToTenant;

class StockLedger extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'product_id',
        'type',
        'qty',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
    ];

    /**
     * Get the product associated with this ledger entry.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
