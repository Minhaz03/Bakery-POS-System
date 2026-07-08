<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\BelongsToTenant;

#[Fillable(['sale_id', 'product_id', 'quantity', 'unit_price', 'subtotal'])]
class SaleItem extends Model
{
    use BelongsToTenant;
    protected function casts(): array
    {
        return [
            'quantity'   => 'decimal:3',
            'unit_price' => 'decimal:2',
            'subtotal'   => 'decimal:2',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
