<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'voucher_no',
        'paid_amount',
        'paid_date',
        'discount'
    ];

    protected $casts = [
        'paid_amount' => 'decimal:2',
        'paid_date' => 'date',
        'discount' => 'decimal:2'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
