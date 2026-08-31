<?php

namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\LabelItem;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Client;
use App\Models\ProductionBatch;
use TCPDF;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LabelController extends Controller
{
    public function unitLabelCreate()
    {
        $products = Product::where('category', 'RF Cable Assembly')
            ->select('id', 'our_part_no', 'category')
            ->orderBy('our_part_no')
            ->get();
        $clients = Client::select('id', 'name')->get();
        $categories = ['RF Cable Assembly'];
        return view('labels.create-unit', compact('products', 'clients', 'categories'));
    }

    public function boxLabelCreate()
    {
        return redirect()->route('labels.studio');
    }

    public function categoryBoxLabelCreate(string $category)
    {
        abort_unless(in_array($category, Product::categories(), true), 404);

        return redirect()->route('labels.studio', ['category' => $category]);
    }

    public function serialTraceability(Request $request)
    {
        $categories = Product::categories();
        $products = Product::orderBy('our_part_no')->get(['id', 'our_part_no', 'category']);
        $clients = Client::orderBy('name')->get(['id', 'name']);

        $items = LabelItem::with(['product', 'label.client', 'label.productionBatch'])
            ->when($request->filled('category'), fn ($query) => $query->whereHas('product', fn ($product) => $product->where('category', $request->category)))
            ->when($request->filled('product_id'), fn ($query) => $query->where('product_id', $request->product_id))
            ->when($request->filled('client_id'), fn ($query) => $query->whereHas('label', fn ($label) => $label->where('client_id', $request->client_id)))
            ->when($request->filled('serial'), fn ($query) => $query->where(function ($serialQuery) use ($request) {
                $serialQuery->where('serial_no', $request->serial)
                    ->orWhere('item_code', 'like', '%' . $request->serial . '%');
            }))
            ->when($request->filled('lot_no'), fn ($query) => $query->whereHas('label', fn ($label) => $label->where('lot_no', 'like', '%' . $request->lot_no . '%')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('labels.traceability', compact('items', 'categories', 'products', 'clients'));
    }

    public function categoryLabelCreate(string $category)
    {
        abort_unless(in_array($category, Product::categories(), true) && $category !== 'RF Cable Assembly', 404);

        $products = Product::where('category', $category)->orderBy('our_part_no')->get(['id', 'our_part_no', 'description']);
        $clients = Client::select('id', 'name')->orderBy('name')->get();
        $batches = ProductionBatch::with('product')
            ->whereHas('product', fn ($query) => $query->where('category', $category))
            ->whereIn('status', ['in_progress', 'completed'])
            ->latest()
            ->get();

        return view('labels.create-category', compact('category', 'products', 'clients', 'batches'));
    }


    public function getLabelDatatable(Request $request)
    {
        if ($request->ajax()) {

            $labels = Label::with([
                'client'
            ])->latest();

            return DataTables::of($labels)

                ->addColumn('lot_no', function ($label) {
                    return $label->lot_no;
                })

                ->addColumn('client_name', function ($label) {
                    return $label->client->name ?? '-';
                })

                ->addColumn('category', function ($label) {
                    return $label->category ?? '-';
                })

                ->addColumn('label_type', function ($label) {
                    return ucfirst($label->label_type);
                })

                ->addColumn('actions', function ($label) {
                    $viewUrl = route('labels.show', $label->id);
                    $printUrl = route('labels.print-unit', $label->id);
                    $deleteUrl = route('labels.destroy', $label->id);
                    $csrf = csrf_token();

                    return '
                <div class="d-flex gap-1 justify-content-center">
                    <a href="' . $viewUrl . '" 
                       class="btn btn-sm btn-info" 
                       title="View Lot Details">
                        <i class="fa fa-eye"></i>
                    </a>
                    <a href="' . $printUrl . '" 
                       class="btn btn-sm btn-warning" 
                       title="Print Lot Stickers">
                        <i class="fa fa-print"></i>
                    </a>
                    <form action="' . $deleteUrl . '" method="POST" style="display:inline"
                        onsubmit="return confirm(\'Are you sure you want to delete this label?\')">
                        <input type="hidden" name="_token" value="' . $csrf . '">
                        <input type="hidden" name="_method" value="DELETE">
                        <button class="btn btn-sm btn-danger" title="Delete">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                    </div>
                ';
                })

                ->rawColumns(['actions'])
                ->make(true);
        }
    }




    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'lot_no' => 'nullable|string',
    //         'notes' => 'nullable|string',
    //         'client_id' => 'required|exists:clients,id',

    //         'products' => 'required|array|min:1',
    //         'products.*' => 'required|exists:products,id',

    //         'quantities' => 'required|array|min:1',
    //         'quantities.*' => 'required|integer|min:1',

    //         'prefixes' => 'nullable|array',
    //     ]);

    //     DB::transaction(function () use ($data) {

    //         $label = Label::create([
    //             'lot_no' => $data['lot_no'],
    //             'notes' => $data['notes'],
    //             'client_id' => $data['client_id'],
    //         ]);

    //         foreach ($data['products'] as $index => $productId) {

    //             $qty = $data['quantities'][$index];
    //             $prefix = $data['prefixes'][$index] ?? 'AUTO';

    //             for ($i = 1; $i <= $qty; $i++) {

    //                 $serial = $this->generateItemCode($prefix);

    //                 $label->labelItems()->create([
    //                     'product_id' => $productId,
    //                     'item_code' => $serial,
    //                 ]);
    //             }
    //         }
    //     });


    //     return redirect()
    //         ->back()
    //         ->with('success', 'Label Created Successfully!');


    // }



    // private function generateItemCode(string $prefix): string
    // {
    //     /*
    //         Example input:
    //         TE-202512XX
    //         XX is just notation and ignored
    //     */

    //     // Remove trailing X (any count)
    //     $basePrefix = preg_replace('/X+$/i', '', $prefix);

    //     // Get the highest existing numeric suffix
    //     $lastNumber = LabelItem::where('item_code', 'like', $basePrefix . '%')
    //         ->selectRaw("
    //         CAST(
    //             SUBSTRING(item_code, LENGTH(?) + 1)
    //             AS UNSIGNED
    //         ) as num
    //     ", [$basePrefix])
    //         ->orderByDesc('num')
    //         ->value('num');

    //     // Next serial number
    //     $nextNumber = ($lastNumber ?? 0) + 1;

    //     // NO padding, NO zeros
    //     return $basePrefix . $nextNumber;
    // }





    private function generateItemCode(string $prefix, ?int $clientId): array
{
    /*
        Example input:
        $prefix = "TE-202512"
    */

    // 🔒 Lock rows to avoid race conditions
    $lastSerial = LabelItem::lockForUpdate()
        ->whereHas('label', function ($q) use ($clientId) {
            $q->where('client_id', $clientId);
        })
        ->max('serial_no');

    // Next serial (GLOBAL PER CLIENT)
    $nextSerial = ($lastSerial ?? 0) + 1;

    return [
        'serial_no' => $nextSerial,
        'item_code' => $prefix . $nextSerial,
    ];
}

private function generateProductItemCode(int $productId, string $prefix): array
{
    $lastSerial = LabelItem::lockForUpdate()
        ->where('product_id', $productId)
        ->max('serial_no');

    $nextSerial = ($lastSerial ?? 0) + 1;

    return [
        'serial_no' => $nextSerial,
        'item_code' => $prefix . $nextSerial,
    ];
}

public function storeCategory(Request $request, string $category)
{
    abort_unless(in_array($category, Product::categories(), true) && $category !== 'RF Cable Assembly', 404);

    $data = $request->validate([
        'lot_no' => 'nullable|string|max:100',
        'notes' => 'nullable|string',
        'client_id' => 'nullable|exists:clients,id',
        'production_batch_id' => 'required|exists:production_batches,id',
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
        'prefix' => 'nullable|string|max:100',
    ]);

    $batch = ProductionBatch::with('product')->findOrFail($data['production_batch_id']);
    abort_unless($batch->product_id == $data['product_id'] && $batch->product->category === $category, 422);
    abort_if((int) $data['quantity'] > (int) $batch->quantity_produced, 422, 'Label quantity cannot exceed production quantity.');

    DB::transaction(function () use ($data, $batch) {
        $label = Label::create([
            'lot_no' => $data['lot_no'] ?: ($batch->lot_no ?: $batch->batch_no),
            'notes' => $data['notes'] ?? null,
            'client_id' => $data['client_id'],
            'category' => $batch->product->category,
            'production_batch_id' => $batch->id,
        ]);

        $prefix = $data['prefix'] ?: ($batch->product->our_part_no . '-' . $batch->batch_no . '-');
        for ($serial = 0; $serial < $data['quantity']; $serial++) {
            $code = $this->generateProductItemCode($batch->product_id, $prefix);
            $label->labelItems()->create([
                'product_id' => $batch->product_id,
                'serial_no' => $code['serial_no'],
                'item_code' => $code['item_code'],
            ]);
        }
    });

    return redirect()->route('labels.category.create', ['category' => $category])->with('success', 'Production labels created successfully.');
}

public function store(Request $request)
{
    $data = $request->validate([
        'lot_no' => 'nullable|string',
        'notes' => 'nullable|string',
        'client_id' => 'nullable|exists:clients,id',
        'category' => 'required|in:RF Antenna,RF Cable Assembly,Microwave Devices,IoT',

        'products' => 'required|array|min:1',
        'products.*' => [
            'required',
            Rule::exists('products', 'id')->where(fn ($query) => $query->where('category', $request->input('category'))),
        ],

        'quantities' => 'required|array|min:1',
        'quantities.*' => 'required|integer|min:1',

        'prefixes' => 'nullable|array',
    ]);

    DB::transaction(function () use ($data) {

        $label = Label::create([
            'lot_no'    => $data['lot_no'],
            'notes'     => $data['notes'],
            'client_id' => $data['client_id'],
            'category'  => $data['category'],
        ]);

        foreach ($data['products'] as $index => $productId) {

            $qty    = $data['quantities'][$index];
            $prefix = $data['prefixes'][$index] ?? 'TE-202512';

            for ($i = 1; $i <= $qty; $i++) {

                $code = $this->generateItemCode($prefix, $data['client_id']);

                $label->labelItems()->create([
                    'product_id' => $productId,
                    'serial_no'  => $code['serial_no'],
                    'item_code'  => $code['item_code'],
                ]);
            }
        }
    });

    return redirect()
        ->back()
        ->with('success', 'Label Created Successfully!');
}



    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $label = Label::findOrFail($id);

            // Delete related label items first
            $label->labelItems()->delete();

            // Delete label
            $label->delete();
        });

        return redirect()->back()->with('success', 'Label Deleted Successfully!');
    }


    public function boxLabelSearch(Request $request)
    {
        abort_unless(!$request->filled('category') || in_array($request->category, Product::categories(), true), 404);

        return redirect()->route('labels.studio', $request->only(['category', 'lot_no']));
    }

    public function ajaxLotSearch(Request $request)
    {
        $q = $request->get('q');
        $category = $request->get('category');

        if (!$q) {
            return response()->json([]);
        }

        $lots = Label::query()
            ->select('id', 'client_id', 'lot_no', 'category')
            ->with([
                'client:id,name'
            ])
            ->where('lot_no', 'LIKE', "%{$q}%")
            ->when($category, fn ($query) => $query->where('category', $category))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($lots);
    }


    public function printUnitLabels($id)
    {
        $label = Label::with('client', 'labelItems.product', 'labelItems.product.clientMappings')->findOrFail($id);

        $pdf = new TCPDF('P', 'mm', 'A3');
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator(auth()->user()->name);
        $pdf->SetAuthor('TE Tech Solution - Inventory ERP');
        $pdf->SetTitle("Label - Lot {$label->lot_no}");
        $pdf->SetSubject('Unit Product Labels');
        $pdf->SetKeywords('Labels, Barcode, QR');
        $pdf->AddPage();

        $labelWidth = 120;
        $labelHeight = 70;
        $gapX = 10;
        $gapY = 10;
        $startX = 10;
        $startY = 10;

        $col = 0;
        $row = 0;
        $count = 0;

        foreach ($label->labelItems as $item) {
            $partNo = $item->product->our_part_no;
            $clientPartNo = optional($item->product->clientMappings->where('client_id', $label->client_id)->first())->client_part_no;
            $clientName = $label->client->name ?? null;
            $itemCode = $item->item_code;


            $x = $startX + ($col * ($labelWidth + $gapX));
            $y = $startY + ($row * ($labelHeight + $gapY));

            /* ================= BORDER ================= */
            $pdf->SetDrawColor(0, 153, 204);
            $pdf->SetLineWidth(0.6);
            $pdf->RoundedRect($x, $y, $labelWidth, $labelHeight, 2);

            /* ================= FONTS ================= */
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', '', 6);

            /* ================= LEFT SIDE ================= */
            $pdf->SetFont('helvetica', 'B', 7);
            $pdf->Text($x + 1, $y + 3, "(1P) TE Part No.: $partNo");

            $pdf->write1DBarcode(
                $partNo,
                'C128',
                $x + 2,
                $y + 6,
                55,
                7,
                0.3,
                ['text' => false]
            );


            if (filled($clientName)) {
                $clientLine = '(2P) ' . $clientName;
                if (filled($clientPartNo)) {
                    $clientLine .= ' Part No: ' . $clientPartNo;
                }
                $pdf->Text($x + 1, $y + 15, $clientLine);
                if (filled($clientPartNo)) {
                    $pdf->write1DBarcode((string) $clientPartNo, 'C128', $x + 2, $y + 18, 40, 7, 0.3, ['text' => false]);
                }
            }

            $pdf->SetFont('helvetica', '', 7);
            $pdf->Text($x + 1, $y + 27, "(1T) Lot No.: {$label->lot_no}");
            $pdf->write1DBarcode(
                $label->lot_no,
                'C128',
                $x + 2,
                $y + 30,
                40,
                7,
                0.3,
                ['text' => false]
            );

            $pdf->Text($x + 1, $y + 39, "(16D) Item Code.: {$itemCode}");
            $pdf->write1DBarcode(
                $itemCode,
                'C128',
                $x + 2,
                $y + 42,
                40,
                7,
                0.3,
                ['text' => false]
            );


            $pdf->Text($x + 1, $y + 51, "(Q) Quantity: 1 pcs.");
            $pdf->write1DBarcode(
                (string) 1,
                'C128',
                $x + 2,
                $y + 54,
                10,
                7,
                0.3,
                ['text' => false]
            );
            /* ================= RIGHT SIDE ================= */



            $pdf->Image(public_path('images/client-logo.jpg'), $x + 101, $y + 0.5, 16);

            $qrLines = ['TE PART NO: ' . $partNo];
            if (filled($clientName)) $qrLines[] = 'CLIENT: ' . $clientName;
            if (filled($clientPartNo)) $qrLines[] = 'CLIENT PART NO: ' . $clientPartNo;
            $qrLines[] = 'ITEM CODE: ' . $itemCode;
            $qrLines[] = 'LOT: ' . $label->lot_no;
            $qrLines[] = 'QTY: 1';
            $qrLines[] = 'DESC: ' . ($item->product->description ?? '');
            $qrData = implode("\n", $qrLines);


            $pdf->write2DBarcode($qrData, 'QRCODE,H', $x + 96, $y + 18, 20, 20);

            $pdf->SetFont('helvetica', 'I', 8);
            $pdf->MultiCell(60, 10, $item->product->description, 0, 'L', false, 1, $x + 60, $y + 45);

            // ICONS
            $pdf->Image(public_path('images/make-in-india.jpg'), $x + 76, $y + 62, 15);
            $pdf->Image(public_path('images/msl1.jpg'), $x + 92, $y + 62, 7);
            $pdf->Image(public_path('images/rohs.jpg'), $x + 100, $y + 61.5, 8);
            $pdf->Image(public_path('images/reach.jpg'), $x + 108, $y + 61.5, 8);

            /* ================= GRID MOVE ================= */
            $col++;
            if ($col == 2) {
                $col = 0;
                $row++;
            }

            $count++;
            if ($count % 10 == 0) {
                $pdf->AddPage();
                $row = 0;
                $col = 0;
            }
        }

        $fileName = "Labels_{$label->label_type}_Lot_{$label->lot_no}.pdf";

        // return response($pdf->Output($fileName, 'S'))
        //     ->header('Content-Type', 'application/pdf')
        //     ->header('Content-Disposition', 'inline; filename="' . $fileName . '"');

        //for testing using this return
        return response($pdf->Output("Labels-{$label->lot_no}.pdf", 'S'))
            ->header('Content-Type', 'application/pdf');

    }



public function printBoxLabels(Request $request, $id)
{
    $label = Label::with('client', 'labelItems.product', 'labelItems.product.clientMappings')
        ->findOrFail($id);

    if ($request->filled('category')) {
        abort_unless($request->category === ($label->category ?: $label->labelItems->first()?->product?->category), 422);
    }

    $productsInput = $request->input('products', []);

    if (empty($productsInput)) {
        abort(400, 'No products selected');
    }

    /* ================= PDF SETUP ================= */

    $pdf = new TCPDF('P', 'mm', 'A4');

    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    $startX = 10;
    $topMargin = 10;
    $bottomMargin = 10;

    $labelWidth = 180;
    $gapBetweenLabels = 5;

    $totalBoxesInLot = 0;

    /* ================= TOTAL BOX COUNT ================= */

    foreach ($productsInput as $productId => $data) {

        $unitsPerBox = (int) ($data['units_per_box'] ?? 0);

        if ($unitsPerBox <= 0) {
            continue;
        }

        $count = $label->labelItems
            ->where('product_id', $productId)
            ->count();

        if ($count === 0) {
            continue;
        }

        $totalBoxesInLot += (int) ceil($count / $unitsPerBox);
    }

    $globalBoxNumber = 1;

    /* ================= FIRST PAGE ================= */

    $pdf->AddPage();
    $pdf->SetY($topMargin);

    foreach ($productsInput as $productId => $data) {

        $unitsPerBox = (int) ($data['units_per_box'] ?? 0);

        if ($unitsPerBox <= 0) {
            continue;
        }

        $items = $label->labelItems
            ->where('product_id', $productId)
            ->values();

        if ($items->isEmpty()) {
            continue;
        }

        $product = $items->first()->product;

        $clientPartNo = optional(
            $product->clientMappings
                ->where('client_id', $label->client_id)
                ->first()
        )->client_part_no;
        $clientName = $label->client->name ?? null;

        $boxes = $items->chunk($unitsPerBox);

        foreach ($boxes as $boxIndex => $boxItems) {

            /* ================= DYNAMIC HEIGHT ================= */

            $baseHeight = 95;

            // 2-column item display
            $rowsNeeded = ceil($boxItems->count() / 2);

            $rowHeight = 12;

            $itemsHeight = $rowsNeeded * $rowHeight;

            // Space for QR + description + icons
            $bottomSectionHeight = 65;

            $labelHeight = max(
                140,
                $baseHeight + $itemsHeight + $bottomSectionHeight
            );

            /* ================= PAGE BREAK ================= */

            $currentY = $pdf->GetY();

            $pageHeight = $pdf->getPageHeight();

            if (($currentY + $labelHeight) > ($pageHeight - $bottomMargin)) {

                $pdf->AddPage();

                $currentY = $topMargin;
            }

            $x = $startX;
            $y = $currentY;

            /* ================= BORDER ================= */

            $pdf->SetDrawColor(0, 153, 204);
            $pdf->SetLineWidth(0.8);

            $pdf->RoundedRect(
                $x,
                $y,
                $labelWidth,
                $labelHeight,
                3
            );

            /* ================= HEADER ================= */

            $pdf->SetFont('helvetica', '', 9);

            $pdf->Text(
                $x + 120,
                $y + 6,
                "BOX {$globalBoxNumber} of {$totalBoxesInLot}"
            );

            /* ================= PART NO ================= */

            $pdf->SetFont('helvetica', 'B', 9);

            $pdf->Text(
                $x + 3,
                $y + 6,
                "(1P) TE Part No: {$product->our_part_no}"
            );

            $pdf->write1DBarcode(
                $product->our_part_no,
                'C128',
                $x + 3,
                $y + 10,
                80,
                10,
                0.4,
                ['text' => false]
            );

            /* ================= CLIENT PART ================= */

            if (filled($clientName)) {
                $clientLine = '(2P) ' . $clientName;
                if (filled($clientPartNo)) {
                    $clientLine .= ' Part No: ' . $clientPartNo;
                }
                $pdf->Text($x + 3, $y + 24, $clientLine);
                if (filled($clientPartNo)) {
                    $pdf->write1DBarcode((string) $clientPartNo, 'C128', $x + 3, $y + 28, 60, 10, 0.4, ['text' => false]);
                }
            }

            /* ================= LOT ================= */

            $pdf->SetFont('helvetica', '', 9);

            $pdf->Text(
                $x + 3,
                $y + 42,
                "(1T) Lot No: {$label->lot_no}"
            );

            $pdf->write1DBarcode(
                $label->lot_no,
                'C128',
                $x + 3,
                $y + 46,
                50,
                9,
                0.4,
                ['text' => false]
            );

            /* ================= QUANTITY ================= */

            $pdf->SetFont('helvetica', 'B', 11);

            $pdf->Text(
                $x + 3,
                $y + 60,
                "(Q) Quantity: {$boxItems->count()} PCS"
            );

            /* ================= ITEM CODES ================= */

            $pdf->SetFont('helvetica', '', 7);

            $startItemsY = $y + 70;

            $currentItemsY = $startItemsY;

            $barcodeH = 7;

            $leftColX = $x + 3;
            $rightColX = $x + 92;

            $itemCodesText = $boxItems
                ->pluck('item_code')
                ->map(fn($c) => "- {$c}")
                ->implode("\n");

            foreach ($boxItems->values() as $index => $item) {

                $isRightColumn = $index % 2 === 1;

                $colX = $isRightColumn ? $rightColX : $leftColX;

                /* ---------- ITEM TEXT ---------- */

                $pdf->Text(
                    $colX,
                    $currentItemsY,
                    "(16D) {$item->item_code}"
                );

                /* ---------- BARCODE ---------- */

                $pdf->write1DBarcode(
                    $item->item_code,
                    'C128',
                    $colX,
                    $currentItemsY + 3,
                    45,
                    $barcodeH,
                    0.3,
                    ['text' => false]
                );

                /* ---------- NEXT ROW ---------- */

                if ($isRightColumn) {
                    $currentItemsY += $rowHeight;
                }
            }

            // If odd count
            if ($boxItems->count() % 2 !== 0) {
                $currentItemsY += $rowHeight;
            }

            /* ================= SAFE LOWER SECTION ================= */

            $afterItemsY = $startItemsY + ($rowsNeeded * $rowHeight) + 5;

            $qrY = max(
                $afterItemsY,
                $y + ($labelHeight - 60)
            );

            /* ================= LOGO ================= */

            $pdf->Image(
                public_path('images/client-logo.jpg'),
                $x + 150,
                $y + 4,
                25
            );

            /* ================= QR CODE ================= */

            $qrLines = ['TE PART NO: ' . $product->our_part_no];
            if (filled($clientName)) $qrLines[] = 'CLIENT: ' . $clientName;
            if (filled($clientPartNo)) $qrLines[] = 'CLIENT PART NO: ' . $clientPartNo;
            $qrLines[] = 'LOT NO: ' . $label->lot_no;
            $qrLines[] = "BOX {$globalBoxNumber} of {$totalBoxesInLot}";
            $qrLines[] = 'QTY: ' . $boxItems->count() . ' PCS';
            $qrLines[] = 'DESCRIPTION: ' . ($product->description ?? '');
            $qrLines[] = 'ITEM CODES:';
            $qrLines[] = $itemCodesText;
            $qrData = implode("\n", $qrLines);

            $pdf->write2DBarcode(
                $qrData,
                'QRCODE,H',
                $x + 4,
                $qrY,
                50,
                50
            );

            /* ================= DESCRIPTION ================= */

            $pdf->SetFont('helvetica', 'I', 9);

            $pdf->MultiCell(
                80,
                10,
                $product->description,
                0,
                'L',
                false,
                1,
                $x + 80,
                $qrY + 10
            );

            /* ================= ICONS ================= */

            $iconsY = $qrY + 35;

            $pdf->Image(
                public_path('images/make-in-india.jpg'),
                $x + 78,
                $iconsY + 2,
                24
            );

            $pdf->Image(
                public_path('images/msl1.jpg'),
                $x + 104,
                $iconsY,
                14
            );

            $pdf->Image(
                public_path('images/rohs.jpg'),
                $x + 119,
                $iconsY - 1,
                17
            );

            $pdf->Image(
                public_path('images/reach.jpg'),
                $x + 133,
                $iconsY - 1,
                17
            );

            /* ================= MOVE TO NEXT LABEL ================= */

            $pdf->SetY(
                $y + $labelHeight + $gapBetweenLabels
            );

            $globalBoxNumber++;
        }
    }

    return response(
        $pdf->Output(
            "Box_Labels_Lot_{$label->lot_no}.pdf",
            'S'
        )
    )->header('Content-Type', 'application/pdf');
}

    public function studio(Request $request)
    {
        $categories = Product::categories();
        $labels = Label::query()->with('client')->withCount('labelItems')->latest()->get();
        $products = Product::query()
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->category))
            ->orderBy('our_part_no')->get(['id', 'our_part_no', 'category']);
        $labelCount = LabelItem::count();

        return view('labels.studio', compact('categories', 'labels', 'products', 'labelCount'));
    }

    public function printStudio(Request $request)
    {
        $data = $request->validate([
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'integer|exists:label_items,id',
            'mode' => 'required|in:unit,box',
            'fields' => 'required|array|min:1',
            'fields.*' => 'in:part_no,client_name,client_part_no,lot_no,item_code,quantity,description,qr,artwork',
            'page_size' => 'required|in:A4,A3',
            'columns' => 'required|integer|min:1|max:6',
            'rows' => 'required|integer|min:1|max:10',
            'units_per_box' => 'nullable|array',
            'units_per_box.*' => 'nullable|integer|min:1',
        ]);

        $items = LabelItem::with(['label.client', 'product.clientMappings'])
            ->whereIn('id', $data['item_ids'])
            ->orderByRaw('FIELD(id, ' . implode(',', array_map('intval', $data['item_ids'])) . ')')
            ->get();
        abort_if($items->isEmpty(), 422, 'Select at least one label.');

        $cards = $items->map(fn ($item) => ['item' => $item, 'is_box' => false]);
        if ($data['mode'] === 'box') {
            $cards = collect();
            $unitsPerBox = $data['units_per_box'] ?? [];
            $boxNumber = 1;
            $selectedItems = $items->groupBy('product_id');

            foreach ($selectedItems as $productId => $productItems) {
                $size = (int) ($unitsPerBox[$productId] ?? 0);
                abort_if($size < 1, 422, 'Set units per box for every selected product.');
                foreach ($productItems->chunk($size) as $boxItems) {
                    $cards->push([
                        'item' => $boxItems->first(),
                        'items' => $boxItems,
                        'is_box' => true,
                        'box_number' => $boxNumber++,
                        'box_total' => (int) ceil($productItems->count() / $size),
                    ]);
                }
            }
        }

        $pageSizes = ['A4' => [210, 297], 'A3' => [297, 420]];
        [$pageWidth, $pageHeight] = $pageSizes[$data['page_size']];
        $margin = 8;
        $gap = 4;
        $labelWidth = ($pageWidth - (2 * $margin) - (($data['columns'] - 1) * $gap)) / $data['columns'];
        $labelHeight = ($pageHeight - (2 * $margin) - (($data['rows'] - 1) * $gap)) / $data['rows'];
        abort_if($labelWidth < 35 || $labelHeight < 30, 422, 'The selected grid is too small for a readable label.');

        $pdf = new TCPDF('P', 'mm', $data['page_size']);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetCreator(auth()->user()->name);
        $pdf->SetAuthor('TE Tech Solution - Inventory ERP');
        $pdf->SetTitle('Label Studio - ' . $data['page_size']);

        foreach ($cards->chunk($data['columns'] * $data['rows']) as $pageItems) {
            $pdf->AddPage();
            foreach ($pageItems->values() as $index => $card) {
                $item = $card['item'];
                $column = $index % $data['columns'];
                $row = intdiv($index, $data['columns']);
                $x = $margin + ($column * ($labelWidth + $gap));
                $y = $margin + ($row * ($labelHeight + $gap));
                $this->renderStudioCard($pdf, $card, $x, $y, $labelWidth, $labelHeight, $data['fields']);
            }
        }

        return response($pdf->Output('Labels-' . $data['page_size'] . '.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Labels-' . $data['page_size'] . '.pdf"');
    }

    public function studioItems(Request $request)
    {
        $items = LabelItem::query()->with(['label.client', 'product.clientMappings'])
            ->when($request->filled('category'), fn ($query) => $query->whereHas('product', fn ($product) => $product->where('category', $request->category)))
            ->when($request->filled('label_id'), fn ($query) => $query->where('label_id', $request->label_id))
            ->when($request->filled('product_id'), fn ($query) => $query->where('product_id', $request->product_id))
            ->when($request->filled('lot_no'), fn ($query) => $query->whereHas('label', fn ($label) => $label->where('lot_no', 'like', '%' . $request->lot_no . '%')))
            ->latest('label_items.created_at');

        return DataTables::of($items)
            ->addColumn('part_no', fn ($item) => $item->product->our_part_no)
            ->addColumn('category', fn ($item) => $item->product->category)
            ->addColumn('lot_no', fn ($item) => $item->label->lot_no)
            ->addColumn('client_name', fn ($item) => optional($item->label->client)->name ?? '-')
            ->addColumn('client_part_no', fn ($item) => optional($item->product->clientMappings->where('client_id', $item->label->client_id)->first())->client_part_no)
            ->addColumn('item_code', fn ($item) => $item->item_code)
            ->addColumn('description', fn ($item) => $item->product->description ?? '')
            ->addColumn('select_data', function ($item) {
                $mapping = $item->product->clientMappings->where('client_id', $item->label->client_id)->first();
                return [
                    'id' => $item->id,
                    'productId' => $item->product_id,
                    'partNo' => $item->product->our_part_no,
                    'description' => $item->product->description ?? '',
                    'lot' => $item->label->lot_no,
                    'client' => optional($item->label->client)->name,
                    'clientPart' => optional($mapping)->client_part_no,
                    'code' => $item->item_code,
                ];
            })
            ->toJson();
    }

    private function renderStudioCard(TCPDF $pdf, array $card, float $x, float $y, float $width, float $height, array $fields): void
    {
        $item = $card['item'];
        $product = $item->product;
        $mapping = $product->clientMappings->where('client_id', $item->label->client_id)->first();
        $clientPartNo = optional($mapping)->client_part_no;
        $clientName = $item->label->client->name;
        $isBox = $card['is_box'];
        $boxItems = $card['items'] ?? collect([$item]);
        $quantity = $boxItems->count();
        $itemCode = $isBox ? 'BOX ' . $card['box_number'] . '/' . $card['box_total'] : $item->item_code;
        $codes = $boxItems->pluck('item_code')->implode(', ');
        $scale = min($width / 120, $height / ($isBox ? 70 : 70));
        $sx = fn (float $value): float => $x + ($value * $scale);
        $sy = fn (float $value): float => $y + ($value * $scale);
        $font = fn (float $value): float => max(3.5, $value * $scale);
        $show = fn (string $field): bool => in_array($field, $fields, true);

        $pdf->SetDrawColor(0, 153, 204);
        $pdf->SetLineWidth(max(0.25, 0.6 * $scale));
        $pdf->RoundedRect($x, $y, 120 * $scale, 70 * $scale, 2 * $scale);
        $pdf->SetTextColor(0, 0, 0);
        if ($show('part_no')) {
            $pdf->SetFont('helvetica', 'B', $font(7));
            $pdf->Text($sx(1), $sy(3), '(1P) TE Part No.: ' . $product->our_part_no);
            $pdf->write1DBarcode($product->our_part_no, 'C128', $sx(2), $sy(6), 55 * $scale, 7 * $scale, 0.3, ['text' => false]);
        }

        $pdf->SetFont('helvetica', '', $font(7));
        if ($show('client_name') && filled($clientName)) {
            $pdf->Text($sx(1), $sy(15), '(2P) ' . $clientName);
        }
        if ($show('client_part_no') && filled($clientPartNo)) {
            $pdf->Text($sx(1), $sy(20), 'Client Part No: ' . $clientPartNo);
            $pdf->write1DBarcode((string) $clientPartNo, 'C128', $sx(2), $sy(23), 40 * $scale, 7 * $scale, 0.3, ['text' => false]);
        }
        if ($show('lot_no')) {
            $pdf->Text($sx(1), $sy(27), '(1T) Lot No.: ' . $item->label->lot_no);
            $pdf->write1DBarcode($item->label->lot_no, 'C128', $sx(2), $sy(30), 40 * $scale, 7 * $scale, 0.3, ['text' => false]);
        }

        if ($isBox) {
            if ($show('quantity')) {
                $pdf->SetFont('helvetica', 'B', $font(7));
                $pdf->Text($sx(1), $sy(39), '(Q) Quantity: ' . $quantity . ' pcs.');
            }
            if ($show('item_code')) {
                $pdf->Text($sx(1), $sy(46), $itemCode);
                $pdf->SetFont('helvetica', '', $font(5.5));
                $pdf->MultiCell(85 * $scale, 4 * $scale, 'ITEM CODES: ' . $codes, 0, 'L', false, 1, $sx(1), $sy(50));
            }
        } else {
            if ($show('item_code')) {
                $pdf->Text($sx(1), $sy(39), '(16D) Item Code.: ' . $itemCode);
                $pdf->write1DBarcode($itemCode, 'C128', $sx(2), $sy(42), 40 * $scale, 7 * $scale, 0.3, ['text' => false]);
            }
            if ($show('quantity')) {
                $pdf->Text($sx(1), $sy(51), '(Q) Quantity: 1 pcs.');
                $pdf->write1DBarcode('1', 'C128', $sx(2), $sy(54), 10 * $scale, 7 * $scale, 0.3, ['text' => false]);
            }
        }

        $qrSize = 20 * $scale;
        if ($show('artwork')) {
            $pdf->Image(public_path('images/client-logo.jpg'), $sx(101), $sy(0.5), 16 * $scale);
        }
        if ($show('qr')) {
            $qrLines = ['TE PART NO: ' . $product->our_part_no];
            if ($show('client_name') && filled($clientName)) $qrLines[] = 'CLIENT: ' . $clientName;
            if ($show('client_part_no') && filled($clientPartNo)) $qrLines[] = 'CLIENT PART NO: ' . $clientPartNo;
            if ($show('item_code')) $qrLines[] = 'ITEM CODE: ' . $itemCode;
            if ($show('lot_no')) $qrLines[] = 'LOT: ' . $item->label->lot_no;
            if ($show('quantity')) $qrLines[] = 'QTY: ' . $quantity;
            if ($isBox && $show('item_code')) $qrLines[] = 'ITEMS: ' . $codes;
            $pdf->write2DBarcode(implode("\n", $qrLines), 'QRCODE,H', $sx(96), $sy(18), $qrSize, $qrSize);
        }
        if ($show('description')) {
            $pdf->SetFont('helvetica', 'I', $font(8));
            $pdf->MultiCell(60 * $scale, 10 * $scale, $product->description ?? '', 0, 'L', false, 1, $sx(60), $sy(45));
        }

        foreach ($show('artwork') ? ['make-in-india.jpg', 'msl1.jpg', 'rohs.jpg', 'reach.jpg'] : [] as $assetIndex => $asset) {
            $assetPath = public_path('images/' . $asset);
            if (is_file($assetPath)) {
                $pdf->Image($assetPath, $sx(76 + ($assetIndex * 8)), $sy(61.5), 7 * $scale, 0);
            }
        }
    }











    public function show($id)
    {
        $label = Label::with([
            'client',
            'labelItems.product.clientMappings'
        ])->findOrFail($id);

        return view('labels.show', compact('label'));
    }




}