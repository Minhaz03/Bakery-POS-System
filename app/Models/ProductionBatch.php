<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProductionOrder;

class ProductionBatch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'production_order_id',
        'batch_number',
        'qty',
        'manufacturing_date',
        'expiry_date',
    ];

    protected $casts = [
        'produced_qty'       => 'decimal:3',
        'manufacturing_date' => 'date',
        'expiry_date'        => 'date',
    ];

    /**
     * The production order this batch belongs to.
     */
    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class);
    }
}
