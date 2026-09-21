<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'type',
        'voucher_number',
        'voucher_date',
        'customer_type',
        'customer_name',
        'customer_phone',
        'student_id',
        'student_id_number',
        'total_amount',
        'items',
        'payment_method'
    ];

    protected $casts = [
        'items' => 'array',
        'voucher_date' => 'date',
        'total_amount' => 'decimal:2'
    ];
}
