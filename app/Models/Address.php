<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'address_line_1', 'address_line_2', 'city', 'state', 'country', 'postal_code',
    ];

    protected $appends = ['full_address'];

    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);
        return implode(', ', $parts);
    }

    public function billingClients()
    {
        return $this->hasMany(Client::class, 'billing_address_id');
    }

    public function shippingClients()
    {
        return $this->hasMany(Client::class, 'shipping_address_id');
    }
}
