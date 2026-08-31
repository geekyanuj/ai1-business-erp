<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class PurchaseOrderItemController extends Controller
{
    /**
     * Store a new PO Item
     */
    public function store(Request $request)
    {
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'product_id'        => 'required|exists:products,id',
            'quantity'          => 'required|numeric|min:1',
            'unit_price'        => 'required|numeric|min:0',
        ]);

        $item = PurchaseOrderItem::create([
            'purchase_order_id' => $request->purchase_order_id,
            'product_id'        => $request->product_id,
            'quantity'          => $request->quantity,
            'unit_price'        => $request->unit_price,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item added successfully',
            'item'    => $item->load('product'),
        ]);
    }

    /**
     * Update PO Item
     */
    public function update(Request $request, $id)
    {
        $item = PurchaseOrderItem::findOrFail($id);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|numeric|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $item->update([
            'product_id' => $request->product_id,
            'quantity'   => $request->quantity,
            'unit_price' => $request->unit_price,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully',
            'item'    => $item->load('product'),
        ]);
    }

    /**
     * Delete PO Item
     */
    public function destroy($id)
    {
        $item = PurchaseOrderItem::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item deleted successfully',
        ]);
    }
}

