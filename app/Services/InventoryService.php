<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * Increase stock safely
     */
    public function addStock(
        Inventory $inventory,
        int|float $quantity,
        string $movementType,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $remarks = null
    ): void {
        if ($quantity <= 0) {
            throw new Exception('Quantity must be greater than zero.');
        }

        DB::transaction(function () use ($inventory, $quantity, $movementType, $referenceType, $referenceId, $remarks) {

            // Update inventory
            $inventory->increment('quantity_available', $quantity);

            // Log movement
            InventoryMovement::create([
                'inventory_id' => $inventory->id,
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'remarks' => $remarks,
                'created_by' => auth()->id(),
            ]);
        });
    }

    /**
     * Reduce stock safely
     */
    public function removeStock(
        Inventory $inventory,
        int|float $quantity,
        string $movementType,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $remarks = null
    ): void {
        if ($quantity <= 0) {
            throw new Exception('Quantity must be greater than zero.');
        }
        if ($inventory->quantity_available <= 5) {
            NotificationService::notify(
                User::role('admin')->pluck('id'),
                'low_stock',
                "{$inventory->material_name} stock is low ({$inventory->quantity_available})"
            );
        }


        DB::transaction(function () use ($inventory, $quantity, $movementType, $referenceType, $referenceId, $remarks) {

            $availableStock = $inventory->quantity_available - $inventory->quantity_reserved;

            if ($availableStock < $quantity) {
                throw new Exception(
                    "Insufficient stock. Available: {$availableStock}"
                );
            }

            // Update inventory
            $inventory->decrement('quantity_available', $quantity);

            // Log movement
            InventoryMovement::create([
                'inventory_id' => $inventory->id,
                'movement_type' => $movementType,
                'quantity' => -$quantity, // negative for out
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'remarks' => $remarks,
                'created_by' => auth()->id(),
            ]);
        });
    }

    /**
     * Reserve stock (used before invoice / delivery)
     */
    public function reserveStock(Inventory $inventory, int|float $quantity): void
    {
        DB::transaction(function () use ($inventory, $quantity) {

            $availableStock = $inventory->quantity_available - $inventory->quantity_reserved;

            if ($availableStock < $quantity) {
                throw new Exception('Not enough stock to reserve.');
            }

            $inventory->increment('quantity_reserved', $quantity);
        });
    }

    /**
     * Release reserved stock (cancel order)
     */
    public function releaseReservedStock(Inventory $inventory, int|float $quantity): void
    {
        DB::transaction(function () use ($inventory, $quantity) {

            if ($inventory->quantity_reserved < $quantity) {
                throw new Exception('Invalid reserve release quantity.');
            }

            $inventory->decrement('quantity_reserved', $quantity);
        });
    }

    /**
     * Convert reserved stock to sold
     */
    public function shipReservedStock(
        Inventory $inventory,
        int|float $quantity,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): void {
        DB::transaction(function () use ($inventory, $quantity, $referenceType, $referenceId) {

            if ($inventory->quantity_reserved < $quantity) {
                throw new Exception('Not enough reserved stock.');
            }

            $inventory->decrement('quantity_reserved', $quantity);
            $inventory->decrement('quantity_available', $quantity);

            InventoryMovement::create([
                'inventory_id' => $inventory->id,
                'movement_type' => 'sale',
                'quantity' => -$quantity,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'remarks' => 'Shipped from reserved stock',
                'created_by' => auth()->id(),
            ]);
        });
    }
}
