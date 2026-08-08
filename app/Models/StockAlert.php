<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class StockAlert extends Model
{
    use BelongsToTenant;
    
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
