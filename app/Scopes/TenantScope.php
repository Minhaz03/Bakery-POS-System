<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (auth()->check()) {
            $tenantId = auth()->user()->tenant_id ?? 1;
            $builder->where($model->getTable() . '.tenant_id', $tenantId);
        } elseif (session()->has('tenant_id')) {
            $builder->where($model->getTable() . '.tenant_id', session('tenant_id'));
        }
    }
}
