<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class SalesInvoice extends Model
{
    protected $fillable = [
        // Relations
        'client_id',
        'billing_address_id',
        'shipping_address_id',
        'quotation_id',
        'proforma_id',

        // Document info
        'invoice_number',
        'invoice_date',
        'status',

        // Client PO
        'client_po_ref',
        'client_po_pdf',

        // Payment & notes
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

    /* ================= RELATIONS ================= */

    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class, 'sales_invoice_id');
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

    public function quotation()
    {
        return $this->belongsTo(SalesQuotation::class);
    }

    public function proforma()
    {
        return $this->belongsTo(SalesProforma::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
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
