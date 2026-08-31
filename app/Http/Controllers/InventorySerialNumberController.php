<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventorySerialNumberController extends Controller
{
    public function index()
    {
        return view('inventory.index');
    }
}
