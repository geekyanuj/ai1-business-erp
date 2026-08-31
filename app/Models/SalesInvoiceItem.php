<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SalesInvoiceItem extends Model
{
    protected $fillable = [
        'sales_invoice_id',

        'product_id',
        'product_description',

        'quantity',
        'unit_price',

        'taxable_amount',

        'hsn_code',
        'tax_rate',

        'tax_amount',
        'total_with_tax',
    ];

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
