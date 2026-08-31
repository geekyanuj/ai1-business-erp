<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Client;
use App\Models\ProductClientMapping;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductClientMappingController extends Controller
{
    /**
     * Show mapping screen
     */
    public function index()
    {
        $products = Product::orderBy('our_part_no')->get();
        $clients = Client::orderBy('name')->get();

        $mappings = ProductClientMapping::with(['product', 'client'])->get();

        return view('product_client_mappings.index', compact(
            'products',
            'clients',
            'mappings'
        ));
    }



    public function getMappingDatatable(Request $request)
    {
        if ($request->ajax()) {
            // $data = Product::select(['id', 'our_part_no', 'description', 'category', 'specs', 'created_at', 'updated_at',]);
             $mappings = ProductClientMapping::with(['product', 'client'])->get();

            //  dd($mappings);
            return DataTables::of($mappings)
             ->addColumn('actions', function ($mapping) {
                return '<form action="' . route('product-client-mappings.destroy', $mapping->id) . '" method="POST" class="d-inline">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Delete this mapping?\')">
                                <i class="fas fa-x"></i>
                            </button>
                        </form>';})
                ->rawColumns(['actions'])
                ->make(true);
        }
    }



    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'client_id' => 'required|exists:clients,id',
            'client_part_no' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // 🚫 Check if mapping already exists
        $exists = ProductClientMapping::where('product_id', $request->product_id)
            ->where('client_id', $request->client_id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'mapping' => 'This product is already mapped. Delete the existing mapping to change it.'
                ]);
        }

        // ✅ Create only
        ProductClientMapping::create([
            'product_id' => $request->product_id,
            'client_id' => $request->client_id,
            'client_part_no' => $request->client_part_no,
            'notes' => $request->notes,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Product mapped to client successfully');
    }


    /**
     * Delete mapping
     */
    public function destroy($id)
    {
        ProductClientMapping::findOrFail($id)->delete();

        return back()->with('success', 'Mapping deleted');
    }





    /**
     * get Mapping route
     */
    public function getClientPartNo(Request $request)
    {
        $mapping = ProductClientMapping::where('product_id', $request->product_id)
            ->where('client_id', $request->client_id)
            ->first();

        return response()->json([
            'client_part_no' => $mapping?->client_part_no
        ]);
    }
}
