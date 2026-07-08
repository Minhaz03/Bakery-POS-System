<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\BelongsToTenant;

#[Fillable(['name', 'short_name', 'base_unit_id', 'operator', 'conversion_rate'])]
class Unit extends Model
{
    use BelongsToTenant;
    protected function casts(): array
    {
        return [
            'conversion_rate' => 'decimal:4',
        ];
    }

    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function childUnits(): HasMany
    {
        return $this->hasMany(Unit::class, 'base_unit_id');
    }

    public function calculateBaseQuantity($qty)
    {
        if (!$this->base_unit_id) return $qty;
        
        if ($this->operator === '*') {
            return $qty * $this->conversion_rate;
        } elseif ($this->operator === '/') {
            return $qty / $this->conversion_rate;
        }
        return $qty;
    }
}
