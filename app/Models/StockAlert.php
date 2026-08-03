<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAlert extends Model
{
    protected $fillable = [
        'product_id',
        'variant_id',
        'warehouse_id',
        'branch_id',
        'threshold_qty',
        'is_active',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
