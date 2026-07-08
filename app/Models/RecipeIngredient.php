<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\BelongsToTenant;

#[Fillable([
    'recipe_id',
    'product_id',
    'ingredient_name',
    'unit_id',
    'quantity',
    'net_quantity',
    'unit_cost',
    'subtotal',
    'notes',
    'sort_order',
])]
class RecipeIngredient extends Model
{
    use BelongsToTenant;
    protected function casts(): array
    {
        return [
            'quantity'     => 'decimal:3',
            'net_quantity' => 'decimal:3',
            'unit_cost'  => 'decimal:2',
            'subtotal'   => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
