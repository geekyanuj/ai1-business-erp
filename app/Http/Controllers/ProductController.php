<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Activitylog\Models\Activity;
use App\Models\SalesInvoiceItem;
use App\Models\PurchaseOrderItem;
use App\Models\Inventory;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('product.product-index');
    }

    public function getProducts(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::select(['id', 'our_part_no', 'description', 'category', 'specs', 'hsn', 'created_at', 'updated_at',]);
            return DataTables::of($data)
                ->addColumn('actions', function ($product) {
                    return '
                        <button type="button" class="btn btn-sm btn-outline-primary edit-product-btn" 
                                data-id="' . $product->id . '" 
                                data-our_part_no="' . $product->our_part_no . '" 
                                data-description="' . $product->description . '" 
                                data-category="' . $product->category . '" 
                                data-specs="' . $product->specs . '" 
                                data-hsn="' . $product->hsn . '" 
                                title="Edit" data-bs-toggle="modal" data-bs-target="#editProductModal">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="' . route('products.destroy', $product->id) . '" method="POST" class="d-inline">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Delete this product?\')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                        <a href="' . route('products.show', $product->id) . '" class="btn btn-sm btn-outline-warning" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        ';
                })

                ->rawColumns(['actions'])
                ->make(true);
        }
    }



    //----------------product add ----------------------
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'our_part_no' => 'required|string|max:255|unique:products',
            'description' => 'nullable|string|max:512',
            'category' => 'required|in:RF Antenna,RF Cable Assembly,RF Cable,Microwave Devices,IoT',
            'specs' => 'nullable|string',
            'hsn' => 'nullable|string|max:20',
        ]);

        $product = new Product($validated);

        $product->save();

        return redirect()->back()->with('success', 'Product created successfully.');
    }


    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return redirect()->back()->withErrors(['Product not found.']);
        }
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }


    //---------------------Update the product------------------------
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return redirect()->back()->withErrors(['Product not found.']);
        }

        $validated = $request->validate([
            'our_part_no' => 'required|string|max:255|unique:products,our_part_no,' . $id,
            'description' => 'nullable|string|max:512',
            'category' => 'required|in:RF Antenna,RF Cable Assembly,RF Cable,Microwave Devices,IoT',
            'specs' => 'nullable|string',
            'hsn' => 'nullable|string|max:20',
        ]);

        // Capture changes before update
        $changes = [];

        foreach ($validated as $field => $newValue) {
            if ($product->$field != $newValue) {
                $label = ucfirst(str_replace('_', ' ', $field));

                $changes[] = "{$label} changed from \"{$product->$field}\" to \"{$newValue}\"";
            }
        }

        // Update product
        $product->update($validated);

        // Log readable activity
        if (!empty($changes)) {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($product)
                ->withProperties([
                    'changes' => $changes,
                ])
                ->log(
                    "Product updated: " . implode(', ', $changes)
                );
        }

        return redirect()->back()->with('success', 'Product updated successfully.');
    }


    public function show($id)
    {
        $product = Product::findOrFail($id);

        // Inventory overview for this product
        $inventories = Inventory::where('product_id', $product->id)->get();

        // Purchases containing this product
        $purchaseItems = PurchaseOrderItem::with('purchaseOrder')
            ->where('product_id', $product->id)
            ->get();

        // Sales containing this product
        $salesItems = SalesInvoiceItem::with('salesInvoice')
            ->where('product_id', $product->id)
            ->get();

        // Spatie Activity Log for this product
        $activityLogs = Activity::where('subject_type', Product::class)
            ->where('subject_id', $product->id)
            ->orderBy('created_at', 'DESC')
            ->get();

        // QR Code options
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel' => QRCode::ECC_L,
            'scale' => 4,
        ]);

        // Create QR code containing Product Part Number
        $rawSvg = (new QRCode($options))->render($product->our_part_no);
        $qrSvg = html_entity_decode($rawSvg);

        // dd($qrSvg);



        return view('product.product-show', compact(
            'product',
            'inventories',
            'purchaseItems',
            'salesItems',
            'activityLogs',
            'qrSvg',
        ));
    }

    public function getProductsList()
    {
        $products = Product::orderBy('our_part_no')->get(['id', 'our_part_no']);
        return response()->json($products);
    }




}
