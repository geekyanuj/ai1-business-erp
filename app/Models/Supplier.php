<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'gst_number',
        'address_id',
    ];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}

