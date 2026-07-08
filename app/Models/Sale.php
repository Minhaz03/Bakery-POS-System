<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\BelongsToTenant;

#[Fillable([
    'invoice_no',
    'customer_id',
    'sale_date',
    'subtotal',
    'discount_amount',
    'tax_amount',
    'grand_total',
    'amount_tendered',
    'change_amount',
    'payment_method',
    'status',
    'note',
    'created_by',
])]
class Sale extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected function casts(): array
    {
        return [
            'sale_date'       => 'date',
            'subtotal'        => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount'      => 'decimal:2',
            'grand_total'     => 'decimal:2',
            'amount_tendered' => 'decimal:2',
            'change_amount'   => 'decimal:2',
        ];
    }

    /**
     * Auto-generate invoice_no before creating.
     */
    protected static function booted(): void
    {
        static::creating(function (Sale $sale) {
            if (empty($sale->invoice_no)) {
                $year  = now()->year;
                $count = static::whereYear('created_at', $year)->withTrashed()->count() + 1;
                $sale->invoice_no = 'INV-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}
