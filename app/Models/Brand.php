<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToTenant;

#[Fillable(['name', 'logo', 'description'])]
class Brand extends Model
{
    use BelongsToTenant;
}
