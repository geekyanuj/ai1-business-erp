<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrnItemSerial extends Model
{
    protected $fillable = [
        'grn_item_id',
        'inventory_id',
        'supplier_serial',
        'our_serial',
        'status',
    ];

    public function grnItem()
    {
        return $this->belongsTo(GrnItem::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
