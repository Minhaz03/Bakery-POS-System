<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToTenant;

#[Fillable(['name', 'rate', 'is_default'])]
class Tax extends Model
{
    use BelongsToTenant;
    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
            'is_default' => 'boolean',
        ];
    }
}
