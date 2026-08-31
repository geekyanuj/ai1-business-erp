<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BomItem extends Model
{
    protected $fillable = [
        'bom_id',
        'material_name',
        'uom',
        'quantity_per_unit',
    ];

    public function bom()
    {
        return $this->belongsTo(Bom::class);
    }
}


