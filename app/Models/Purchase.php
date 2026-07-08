<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\BelongsToTenant;

#[Fillable([
    'reference_no',
    'supplier_id',
    'purchase_date',
    'subtotal',
    'discount_amount',
    'tax_amount',
    'shipping_cost',
    'grand_total',
    'amount_paid',
    'amount_due',
    'status',
    'payment_status',
    'payment_method',
    'notes',
    'attachment',
    'created_by',
])]
class Purchase extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected function casts(): array
    {
        return [
            'purchase_date'   => 'date',
            'subtotal'        => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount'      => 'decimal:2',
            'shipping_cost'   => 'decimal:2',
            'grand_total'     => 'decimal:2',
            'amount_paid'     => 'decimal:2',
            'amount_due'      => 'decimal:2',
        ];
    }

    /**
     * Auto-generate a reference number before creating.
     */
    protected static function booted(): void
    {
        static::creating(function (Purchase $purchase) {
            if (empty($purchase->reference_no)) {
                $year = now()->year;
                $count = static::whereYear('created_at', $year)->count() + 1;
                $purchase->reference_no = 'PO-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Recalculate totals from line items.
     */
    public function recalculate(): void
    {
        $this->subtotal   = $this->items->sum('subtotal');
        $this->grand_total = $this->subtotal
            - $this->discount_amount
            + $this->tax_amount
            + $this->shipping_cost;
        $this->amount_due = $this->grand_total - $this->amount_paid;
        $this->save();
    }
}
