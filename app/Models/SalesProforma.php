<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class SalesProforma extends Model
{
    protected $table = 'sales_proformas';

    protected $fillable = [
        // Relations
        'client_id',
        'billing_address_id',
        'shipping_address_id',
        'quotation_id',
        'invoice_id',

        // Document info
        'proforma_number',
        'proforma_date',
        'status',


        'client_po_ref',
        'client_po_pdf',

        // Notes
        'payment_mode',
        'remarks',
        'tnc',
        'notes',

        // Amounts
        'subtotal',
        'tax_type',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'grand_total',

        // Audit
        'created_by',
    ];

    /* ==============================
     |  RELATIONSHIPS
     |==============================*/

    public function items()
    {
        return $this->hasMany(SalesProformaItem::class, 'sales_proforma_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function quotation()
    {
        return $this->belongsTo(SalesQuotation::class);
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

    public function invoice()
    {
        return $this->hasOne(SalesInvoice::class, 'proforma_id');
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
