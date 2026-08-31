<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Grn;
use Illuminate\Http\Request;

class GrnController extends Controller
{
    public function index()
    {
        $grns = Grn::with(['purchaseOrder', 'receiver'])
    ->latest()
    ->get();

        return view('grns.index', compact('grns'));
    }

    public function show(Grn $grn)
    {
        $grn->load([
            'items.product',
            'items.serials',
            'purchaseOrder',
            'receiver',
        ]);

        return view('grns.show', compact('grn'));
    }
}
