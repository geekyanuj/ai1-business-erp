<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';

    protected $fillable = [
        // Relations
        'supplier_id',
        'created_by',
        'approved_by',
        'received_by',

        // PO core details
        'po_type',
        'po_number',
        'po_ref',
        'status',

        // Dates
        'ordered_date',
        'approved_on',
        'delivery_date',
        'deliver_to_id',
        'deliver_to_entity_id',
        'received_date',

        // Notes
        'remarks',
        'notes',
        'tnc',

        // Amounts
        'subtotal',

        // Tax details
        'tax_type',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',

        // Final amount
        'grand_total',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function deliveryAddress()
    {
        return $this->belongsTo(Address::class, 'deliver_to_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function grns()
    {
        return $this->hasMany(Grn::class);
    }

    public function activityLogs()
    {
        return $this->morphMany(Activity::class, 'subject')
            ->latest();
    }

    public function communications()
    {
        return $this->morphMany(Communication::class, 'model')->latest();
    }




    //helper functions
    public function totalOrderedQty()
    {
        return $this->items->sum('quantity');
    }

    public function totalReceivedQty()
    {
        return $this->grns->flatMap->items->sum('quantity_received');
    }

    public function isFullyReceived(): bool
    {
        foreach ($this->items as $item) {
            $received = $this->grns
                ->flatMap->items
                ->where('purchase_order_item_id', $item->id)
                ->sum('quantity_received');

            if ($received < $item->quantity) {
                return false;
            }
        }
        return true;
    }


}
