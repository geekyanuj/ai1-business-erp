<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class SalesQuotation extends Model
{

    protected $table = 'sales_quotations';

    protected $fillable = [
        'client_id',
        'billing_address_id',
        'shipping_address_id',
        'proforma_id',
        'invoice_id',
        'quotation_number',
        'quotation_date',
        'status',
        'client_query_from',

        // Amounts
        'subtotal',
        'grand_total',

        // Tax 
        'tax_type',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',

        // Notes
        'remarks',
        'notes',
        'tnc',

        // Audit
        'created_by',
    ];

    /* ==============================
     |  RELATIONSHIPS
     |==============================*/

    public function items()
    {
        return $this->hasMany(SalesQuotationItem::class, 'sales_quotation_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function billingAddress()
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    /**
     * Linked Proforma (if converted)
     */
    public function proforma()
    {
        return $this->hasOne(SalesProforma::class, 'quotation_id');
    }

    /**
     * Linked Tax Invoice (if converted)
     */
    public function invoice()
    {
        return $this->hasOne(SalesInvoice::class, 'quotation_id');
    }

     /* ================= ACTIVITY LOG ================= */

    public function activityLogs()
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function communications()
    {
        return $this->morphMany(Communication::class, 'model')->latest();
    }
}
