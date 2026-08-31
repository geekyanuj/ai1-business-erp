<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Product extends Model
{

    public static function categories(): array
    {
        return ['RF Antenna', 'RF Cable Assembly', 'RF Cable', 'Microwave Devices', 'IoT'];
    }

    protected $table = 'products';

    protected $fillable = [
        'our_part_no',
        'description',
        'category',
        'specs',
        'hsn',
        'created_at',
        'updated_at',
    ];

    public function clientMappings()
    {
        return $this->hasMany(ProductClientMapping::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function totalStock(): float
    {
        return $this->inventories()->sum('quantity_available');
    }

    public function availableStock(): float
    {
        return $this->inventories()
            ->selectRaw('SUM(quantity_available - quantity_reserved) as stock')
            ->value('stock') ?? 0;
    }

    public function productionBatches()
    {
        return $this->hasMany(ProductionBatch::class, 'product_id');
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'product_id');
    }

    public function salesInvoiceItems()
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }

    public function salesProformaItems()
    {
        return $this->hasMany(SalesProformaItem::class);
    }

    public function salesQuotationItems()
    {
        return $this->hasMany(SalesQuotationItem::class);
    }

    public function shipmentItems()
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function boms()
    {
        return $this->hasMany(Bom::class);
    }

    public function activeBom()
    {
        return $this->hasOne(Bom::class)->where('is_active', true);
    }

}
