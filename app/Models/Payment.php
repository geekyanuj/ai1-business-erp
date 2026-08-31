<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'amount',
        'payment_mode',
        'reference_no',
        'paid_at',
        'transaction_id',
        'created_by',
    ];

    public function invoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

