<?php

namespace App\Services;

use App\Models\InventorySerialNumber;
class ClientSerialGenerator
{
    public static function generate($client, $product)
    {
        $sequence = InventorySerialNumber::where('client_id', $client->id)->count() + 1;

        return strtoupper($client->code)
            . '-' . $product->sku
            . '-' . now()->format('Y')
            . '-' . str_pad($sequence, 5, '0', STR_PAD_LEFT);
    }
}
