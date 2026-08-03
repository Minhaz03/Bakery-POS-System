<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    protected $fillable = [
        'product_id',
        'variant_id',
        'warehouse_id',
        'branch_id',
        'quantity',
        'type',
        'reason',
        'status',
        'user_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stockLedgers()
    {
        return $this->morphMany(StockLedger::class, 'reference');
    }
}
