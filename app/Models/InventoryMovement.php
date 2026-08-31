<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $table = 'inventory_movements';

    protected $fillable = [
        'inventory_id',

        // Movement info
        'movement_type',        // purchase, sale, production_in, production_out, adjustment, transfer
        'quantity',             // +ve = IN, -ve = OUT

        // References
        'reference_type',       // sales_invoice, purchase_order, production_order, manual
        'reference_id',

        // Meta
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'float',
    ];

    /* ============================
     | RELATIONSHIPS
     |============================ */

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Polymorphic reference
     * Example: SalesInvoice, PurchaseOrder, ProductionOrder
     */
    public function reference()
    {
        return $this->morphTo(__FUNCTION__, 'reference_type', 'reference_id');
    }

    /* ============================
     | SCOPES
     |============================ */

    public function scopeIn($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeOut($query)
    {
        return $query->where('quantity', '<', 0);
    }

    /* ============================
     | HELPERS
     |============================ */

    public function isIn(): bool
    {
        return $this->quantity > 0;
    }

    public function isOut(): bool
    {
        return $this->quantity < 0;
    }
}
