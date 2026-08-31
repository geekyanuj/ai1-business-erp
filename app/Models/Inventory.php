<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventories';

    public const TYPES = ['raw', 'ready', 'equipment'];

    protected $fillable = [
        'inventory_type',
        'product_id',
        'material_name',
        'uom',
        'quantity_available',
        'quantity_reserved',
        'location',
        'description', // for equipment details
    ];

    protected $casts = [
        'quantity_available' => 'float',
        'quantity_reserved' => 'float',
        'last_updated' => 'datetime',
    ];

    /* ============================
     | RELATIONSHIPS
     |============================ */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function serialNumbers()
    {
        return $this->hasMany(InventorySerialNumber::class);
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /* ============================
     | HELPERS
     |============================ */

    public function getAvailableStockAttribute(): float
    {
        return $this->quantity_available - $this->quantity_reserved;
    }

    public function isLowStock(float $threshold = 0): bool
    {
        return $this->available_stock <= $threshold;
    }
}
