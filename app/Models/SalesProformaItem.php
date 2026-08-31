<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesProformaItem extends Model
{
    protected $table = 'sales_proforma_items';

    protected $fillable = [
        'sales_proforma_id',

        'product_id',

        'quantity',
        'unit_price',

        'discount_percent',
        'discount_amount',
        'taxable_amount',

        'tax_rate',

        'tax_amount',
        'total_with_tax',
    ];

    /* ==============================
     |  RELATIONSHIPS
     |==============================*/

    public function proforma()
    {
        return $this->belongsTo(SalesProforma::class, 'sales_proforma_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
