<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'voucher_no',
        'paid_amount',
        'paid_date'
    ];

    protected $casts = [
        'paid_amount' => 'decimal:2',
        'paid_date' => 'date'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
