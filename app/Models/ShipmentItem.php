<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ShipmentItem extends Model
{
    protected $fillable = [
        'shipment_id', 'product_id', 'lot_no',
        'serial_numbers', 'quantity'
    ];

    protected $casts = [
        'serial_numbers' => 'array'
    ];
}
