<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'source',
        'plugin_name',
        'invoice_id',
        'order_id',
        'customer_email',
        'product',
        'quantity',
        'credit_points',
        'date_purchase'
    ];

    protected $casts = [
        'date_purchase' => 'datetime',
    ];
}
