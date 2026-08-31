<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesQuotationItem extends Model
{
    protected $table = 'sales_quotation_items';

    protected $fillable = [
        'sales_quotation_id',

        // Product info
        'product_id',

        // Quantity & pricing
        'quantity',
        'unit_price',
        'taxable_amount',

        // Discount allowed only in quotation
        'discount_percent',
        'discount_amount',
        
        // Tax
        'tax_rate',
        'tax_amount',

        // Totals
        'total_with_tax',
    ];

    /* ==============================
     |  RELATIONSHIPS
     |==============================*/

    public function quotation()
    {
        return $this->belongsTo(SalesQuotation::class, 'sales_quotation_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    
}
