<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\BelongsToTenant;

#[Fillable(['purchase_id', 'product_id', 'unit_id', 'quantity', 'net_quantity', 'unit_cost', 'subtotal'])]
class PurchaseItem extends Model
{
    use BelongsToTenant;
    protected function casts(): array
    {
        return [
            'quantity'     => 'decimal:3',
            'net_quantity' => 'decimal:3',
            'unit_cost'    => 'decimal:2',
            'subtotal'     => 'decimal:2',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
