<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\BelongsToTenant;

class ProductionBatch extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'batch_code',
        'recipe_id',
        'qty',
        'status',
        'scheduled_at',
        'completed_at',
        'manufacturing_date',
        'expiry_date',
        'wastage_qty',
        'wastage_notes',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at'       => 'datetime',
        'completed_at'       => 'datetime',
        'manufacturing_date' => 'date',
        'expiry_date'        => 'date',
        'qty'                => 'decimal:3',
        'wastage_qty'        => 'decimal:3',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function consumptions()
    {
        return $this->hasMany(ProductionConsumption::class);
    }
}
