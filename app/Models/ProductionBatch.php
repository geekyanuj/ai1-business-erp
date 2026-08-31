<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionBatch extends Model
{
    protected $fillable = [
        'product_id',
        'batch_no',
        'lot_no',
        'quantity_produced',
        'production_date',
        'expiry_date',
        'operator_id',
        'remarks',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
