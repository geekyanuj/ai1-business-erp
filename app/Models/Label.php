<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductionBatch;

class Label extends Model
{
    protected $fillable = [
        'lot_no',
        'notes',
        'client_id', // link to client for mapping
        'category',
        'production_batch_id',
    ];
    
    /**
     * Relationship: a label has many items (stickers)
     */
    public function labelItems()
    {
        return $this->hasMany(LabelItem::class);
    }

    /**
     * Relationship: label belongs to a client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }   

    public function productionBatch()
    {
        return $this->belongsTo(ProductionBatch::class);
    }
}
