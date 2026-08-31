<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Shipment extends Model
{
    protected $fillable = [
        'sales_invoice_id', 'shipment_no',
        'shipped_date', 'courier_name', 'tracking_no'
    ];

    public function items()
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id');
    }
}
