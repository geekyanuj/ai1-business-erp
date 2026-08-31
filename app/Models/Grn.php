<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grn extends Model
{
    protected $fillable = [
        'grn_number',
        'purchase_order_id',
        'received_date',
        'received_by',
        'remarks',
    ];

    public static function generateNumber()
    {
        return 'GRN-' . now()->format('Ymd') . '-' . rand(1000, 9999);
    }
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items()
    {
        return $this->hasMany(GrnItem::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

}
