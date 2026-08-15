<?php

namespace App\Traits;

use App\Models\Tenant;
use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            if (array_key_exists('tenant_id', $model->getAttributes()) && is_null($model->getAttributes()['tenant_id'])) {
                return;
            }

            if (!empty($model->tenant_id)) {
                return;
            }

            if (auth()->check()) {
                $model->tenant_id = auth()->user()->tenant_id ?? 1;
            } elseif (session()->has('tenant_id')) {
                $model->tenant_id = session('tenant_id');
            } else {
                $model->tenant_id = 1;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
