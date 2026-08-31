<?php

namespace App\Http\Controllers;

use App\Models\Bom;
use App\Models\BomItem;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BomController extends Controller
{
    /* =========================
     | LIST
     |========================= */
    public function index()
    {
        return view('production.boms.index');
    }

    public function getDataTable(Request $request)
    {
        $query = Bom::with('product')->latest();

        return DataTables::of($query)
            ->editColumn('product', fn($r) => $r->product->our_part_no ?? '-')
            ->editColumn('is_active', function($r) {
                $statusClass = $r->is_active ? 'success' : 'secondary';
                $statusText = $r->is_active ? 'Active' : 'Inactive';
                return '<span class="badge bg-' . $statusClass . '">' . $statusText . '</span>';
            })
            ->addColumn('actions', fn($r) => 
                '<a href="'.route('production.boms.edit', $r->id).'" class="btn btn-sm btn-outline-primary">Edit</a>'
            )
            ->rawColumns(['is_active', 'actions'])
            ->make(true);
    }

    /* =========================
     | CREATE
     |========================= */
    public function create()
    {
        $products = Product::orderBy('our_part_no')->get();
        $inventories = Inventory::where('inventory_type', 'raw')->get();

        return view('production.boms.create', compact('products', 'inventories'));
    }

    /* =========================
     | STORE
     |========================= */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id|unique:boms,product_id',
            'items' => 'required|array|min:1',
            'items.*.material_name' => 'required|string|max:255',
            'items.*.quantity_per_unit' => 'required|numeric|min:0.0001',
        ]);

        DB::transaction(function () use ($request) {

            $bom = Bom::create([
                'product_id' => $request->product_id,
                'remarks' => $request->remarks,
                'is_active' => true,
            ]);

            foreach ($request->items as $item) {

                $inventory = Inventory::where(
                    'material_name',
                    $item['material_name']
                )->firstOrFail();

                $bom->items()->create([
                    'material_name' => $item['material_name'],
                    'uom' => $inventory->uom, // ✅ AUTO FROM INVENTORY
                    'quantity_per_unit' => $item['quantity_per_unit'],
                ]);
            }
        });

        return redirect()
            ->route('production.boms.index') // ✅ FIXED
            ->with('success', 'BOM created successfully.');
    }


    /* =========================
     | SHOW
     |========================= */
    public function show(Bom $bom)
    {
        $bom->load('product', 'items');

        return view('boms.show', compact('bom'));
    }

    /* =========================
     | EDIT
     |========================= */
    public function edit(Bom $bom)
    {
        $bom->load('items');
        $products = Product::orderBy('our_part_no')->get();

        return view('boms.edit', compact('bom', 'products'));
    }

    /* =========================
     | UPDATE
     |========================= */
    public function update(Request $request, Bom $bom)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.material_name' => 'required|string|max:255',
            'items.*.uom' => 'required|string|max:20',
            'items.*.quantity_per_unit' => 'required|numeric|min:0.0001',
            'is_active' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request, $bom) {

            $bom->update([
                'is_active' => $request->boolean('is_active'),
            ]);

            // 🔥 Replace items safely
            $bom->items()->delete();

            foreach ($request->items as $item) {
                $bom->items()->create([
                    'material_name' => $item['material_name'],
                    'uom' => $item['uom'],
                    'quantity_per_unit' => $item['quantity_per_unit'],
                ]);
            }
        });

        return redirect()
            ->route('boms.show', $bom->id)
            ->with('success', 'BOM updated successfully.');
    }

    /* =========================
     | DELETE
     |========================= */
    public function destroy(Bom $bom)
    {
        $bom->delete();

        return redirect()
            ->route('boms.index')
            ->with('success', 'BOM deleted successfully.');
    }
}


