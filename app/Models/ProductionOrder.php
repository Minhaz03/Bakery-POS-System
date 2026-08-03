<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\BelongsToTenant;

class ProductionOrder extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'reference_no',
        'recipe_id',
        'planned_quantity',
        'actual_quantity',
        'branch_id',
        'warehouse_id',
        'planned_date',
        'produced_at',
        'status',
        'cost_per_unit',
        'total_cost',
        'produced_by',
        'notes',
    ];

    protected $casts = [
        'planned_date'     => 'date',
        'produced_at'      => 'datetime',
        'planned_quantity' => 'decimal:3',
        'actual_quantity'  => 'decimal:3',
        'cost_per_unit'    => 'decimal:2',
        'total_cost'       => 'decimal:2',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'produced_by');
    }

    public function ingredients()
    {
        return $this->hasMany(ProductionOrderIngredient::class);
    }

    public function batches()
    {
        return $this->hasMany(ProductionBatch::class);
    }
}
