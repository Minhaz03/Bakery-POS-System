<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class ProductionOrderIngredient extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'production_order_id',
        'ingredient_id',
        'required_qty',
        'consumed_qty',
        'waste_qty',
    ];

    protected $casts = [
        'required_qty' => 'decimal:3',
        'consumed_qty' => 'decimal:3',
        'waste_qty'    => 'decimal:3',
    ];

    public function order()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(Product::class, 'ingredient_id');
    }
}
