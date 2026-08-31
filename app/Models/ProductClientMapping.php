<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductClientMapping extends Model
{
    protected $fillable = [
        'product_id', 'client_id', 'client_part_no', 'notes'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
