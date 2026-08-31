<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GrnItemSerial;
use Illuminate\Http\Request;

class SupplierSerialSearchController extends Controller
{
    public function index(Request $request)
    {
        $serial = $request->get('serial');

        $result = null;

        if ($serial) {
            $result = GrnItemSerial::with([
                'grnItem.grn.purchaseOrder',
                'grnItem.product',
            ])
            ->where('supplier_serial', $serial)
            ->first();
        }

        return view('supplier.serials.search', compact('serial', 'result'));
    }
}

