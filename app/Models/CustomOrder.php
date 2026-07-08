<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class CustomOrder extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_id',
        'details',
        'delivery_date',
        'total_price',
        'advance_payment',
        'status',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'total_price' => 'decimal:2',
        'advance_payment' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }}
