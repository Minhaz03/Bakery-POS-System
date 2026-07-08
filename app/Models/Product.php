<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\BelongsToTenant;

#[Fillable([
    'name',
    'sku',
    'barcode',
    'category_id',
    'brand_id',
    'unit_id',
    'tax_id',
    'description',
    'image',
    'cost_price',
    'sale_price',
    'mrp_price',
    'stock_qty',
    'alert_qty',
    'reorder_qty',
    'is_active',
    'is_pos_enabled',
    'product_type',
])]
class Product extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'mrp_price' => 'decimal:2',
            'stock_qty' => 'decimal:3',
            'alert_qty' => 'decimal:3',
            'reorder_qty' => 'decimal:3',
            'is_active' => 'boolean',
            'is_pos_enabled' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }
}
