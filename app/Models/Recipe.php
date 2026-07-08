<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\BelongsToTenant;

#[Fillable([
    'name',
    'product_id',
    'description',
    'category',
    'prep_time',
    'bake_time',
    'yield_qty',
    'yield_unit',
    'instructions',
    'notes',
    'estimated_cost',
    'is_active',
])]
class Recipe extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected function casts(): array
    {
        return [
            'estimated_cost' => 'decimal:2',
            'yield_qty'      => 'integer',
            'is_active'      => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class)->orderBy('sort_order');
    }
}
