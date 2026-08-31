<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabelItem extends Model
{
    protected $fillable = [
        'label_id',
        'product_id',
        'serial_no',
        'item_code',
    ];

    /**
     * Relationship: a label item belongs to a label
     */
    public function label()
    {
        return $this->belongsTo(Label::class);
    }

    /**
     * Relationship: a label item belongs to a product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship: get the client part number for this item
     * assuming label has a client_id
     */
    public function clientPartMapping()
    {
        return $this->hasOneThrough(
            ProductClientMapping::class,
            Product::class,
            'id',           // Foreign key on Product table
            'product_id',   // Foreign key on ProductClientMapping table
            'product_id',   // Local key on LabelItem
            'id'            // Local key on Product
        )->where('client_id', $this->label->client_id ?? null);
    }
}
