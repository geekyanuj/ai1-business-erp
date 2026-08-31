<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventorySerialNumber extends Model
{
    protected $table = 'inventory_serials';

    protected $fillable = [
        'inventory_id',
        'serial_number',
        'status',
        'supplier_serial_number ',
    ];

    /* ============================
     | RELATIONSHIPS
     |============================ */

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /* ============================
     | SCOPES
     |============================ */

    public function scopeInStock($query)
    {
        return $query->where('status', 'in_stock');
    }

    public function scopeReserved($query)
    {
        return $query->where('status', 'reserved');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }
}
