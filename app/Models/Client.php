<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'name', 'contact_person', 'email', 'phone', 'billing_address_id', 'shipping_address_id', 'gst_number', 'notes',
    ];

    public function productMappings()
    {
        return $this->hasMany(ProductClientMapping::class);
    }

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'client_id');
    }

    public function addresses()
    {
        return $this->belongsToMany(Address::class, 'client_address');
    }

    public function billingAddress()
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }
}

