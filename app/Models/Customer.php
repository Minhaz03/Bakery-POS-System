<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\BelongsToTenant;

#[Fillable(['name', 'phone', 'email', 'address', 'date_of_birth', 'loyalty_points', 'total_spent', 'is_active'])]
class Customer extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'loyalty_points' => 'integer',
            'total_spent' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
