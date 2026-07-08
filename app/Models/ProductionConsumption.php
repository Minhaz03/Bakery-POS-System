<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class ProductionConsumption extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'production_batch_id',
        'product_id',
        'qty',
        'unit_cost',
        'total_cost',
    ];

    public function batch()
    {
        return $this->belongsTo(ProductionBatch::class, 'production_batch_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
