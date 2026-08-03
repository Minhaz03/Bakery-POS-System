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
        'variant_id',
        'warehouse_id',
        'branch_id',
        'type',
        'quantity',
        'unit_cost',
        'reference_id',
        'reference_type',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
    ];

    /**
     * Get the parent reference model (e.g. StockAdjustment, Sale, etc.).
     */
    public function reference()
    {
        return $this->morphTo();
    }

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
