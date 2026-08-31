<?php

namespace App\Http\Controllers;

use App\Models\Communication;
use App\Models\Grn;
use App\Models\GrnItemSerial;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Company;
use App\Services\PurchaseOrderPdfService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Log;
use Mail;
use App\Mail\PurchaseOrderMail;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class PurchaseOrderController extends Controller
{



    protected PurchaseOrderPdfService $pdfService;
    protected InventoryService $inventoryService;

    public function __construct(PurchaseOrderPdfService $pdfService, InventoryService $inventoryService)
    {
        $this->pdfService = $pdfService;
        $this->inventoryService = $inventoryService;
    }

    /* ----------------------------------------------------------
     * LIST PAGE (Datatable)
     * ---------------------------------------------------------- */
    public function index()
    {
        $suppliers = Supplier::all();
        $clients = \App\Models\Client::all();
        return view('purchase_orders.index', compact('suppliers', 'clients'));
    }

    public function getPurchaseOrdersDataTable(Request $request)
    {
        if ($request->ajax()) {

            $data = PurchaseOrder::with('supplier')->select([
                'purchase_orders.id',
                'purchase_orders.po_number',
                'purchase_orders.po_type',
                'purchase_orders.supplier_id',
                'purchase_orders.ordered_date',
                'purchase_orders.status',
                'purchase_orders.remarks',
            ]);

            return DataTables::of($data)

                ->addColumn('actions', function ($po) {
                    return '
                    <a href="' . route('purchase-orders.show', $po->id) . '" 
                       class="btn btn-sm btn-outline-warning" 
                       title="View">
                       <i class="fas fa-eye"></i>
                    </a>
                ';
                })

                ->rawColumns(['actions'])
                ->make(true);
        }
    }


    /* ----------------------------------------------------------
     * GENERATE PO NUMBER
     * ---------------------------------------------------------- */
    public function generatePONumber()
    {
        $company = Company::firstOrFail();
        $prefix = $company->company_code;

        $now = now();
        $year = $now->year;
        $month = $now->month;

        // Financial Year calculation
        if ($month >= 4) {
            $fyStart = substr($year, -2);
            $fyEnd = substr($year + 1, -2);
        } else {
            $fyStart = substr($year - 1, -2);
            $fyEnd = substr($year, -2);
        }

        $financialYear = "{$fyStart}-{$fyEnd}";

        // Get last PO of the SAME financial year
        $last = PurchaseOrder::where('po_number', 'like', "%{$financialYear}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastIncrement = (int) substr($last->po_number, -4);
            $newIncrement = str_pad($lastIncrement + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newIncrement = '0001';
        }

        return response()->json([
            'po_number' => "{$prefix}-PO-{$financialYear}-{$newIncrement}",
        ]);
    }




    /* ----------------------------------------------------------
     * STORE PO + ITEMS
     * ---------------------------------------------------------- */
    public function store(Request $request)
    {
        try {

            /* ----------------------------------------------------
               VALIDATION
            ---------------------------------------------------- */
            $validated = $request->validate([
                'supplier_id' => 'required|exists:suppliers,id',
                'po_number' => 'required|string|unique:purchase_orders,po_number',
                'ordered_date' => 'nullable|date',
                'delivery_date' => 'nullable|date',
                'po_type' => 'required|in:raw,ready',
                'quote_ref' => 'required|string',
                'status' => 'required|in:draft,approved,received',

                'remarks' => 'nullable|string',
                'deliver_to_id' => 'required|exists:addresses,id',
                'deliver_to_entity_id' => 'required',
                'notes' => 'nullable|string',
                'tnc' => 'nullable|string',

                'subtotal' => 'required|numeric|min:0',
                'tax_type' => 'required|in:cgst_sgst,igst',
                'grand_total' => 'required|numeric|min:0',

                // Items
                'items.product_name.*' => 'required|string',
                'items.product_description.*' => 'nullable|string',
                'items.hsn_code.*' => 'nullable|string|max:20',
                'items.quantity.*' => 'required|numeric|min:1',
                'items.unit_price.*' => 'required|numeric|min:0',
                'items.uom.*' => 'nullable|string',
                'items.tax_rate.*' => 'required|numeric|min:0',
                'items.tax_amount.*' => 'required|numeric|min:0',
                'items.total_with_tax.*' => 'required|numeric|min:0',
            ]);

            /* ----------------------------------------------------
               CALCULATE TAX TOTALS FROM ITEMS
            ---------------------------------------------------- */
            $totalTax = array_sum($validated['items']['tax_amount']);

            $cgstAmount = 0;
            $sgstAmount = 0;
            $igstAmount = 0;

            if ($validated['tax_type'] === 'cgst_sgst') {
                $cgstAmount = $totalTax / 2;
                $sgstAmount = $totalTax / 2;
            } else {
                $igstAmount = $totalTax;
            }

            /* ----------------------------------------------------
               CREATE PURCHASE ORDER
            ---------------------------------------------------- */
            $po = PurchaseOrder::create([
                'supplier_id' => $validated['supplier_id'],
                'po_number' => $validated['po_number'],
                'ordered_date' => $validated['ordered_date'],
                'delivery_date' => $validated['delivery_date'],
                'po_type' => $validated['po_type'],
                'quote_ref' => $validated['quote_ref'],
                'status' => $validated['status'],

                'remarks' => $validated['remarks'],
                'deliver_to_id' => $validated['deliver_to_id'],
                'deliver_to_entity_id' => $validated['deliver_to_entity_id'],
                'notes' => $validated['notes'],
                'tnc' => $validated['tnc'],

                'subtotal' => $validated['subtotal'],
                'tax_type' => $validated['tax_type'],

                'cgst_rate' => 0,
                'sgst_rate' => 0,
                'igst_rate' => 0,

                'cgst_amount' => $cgstAmount,
                'sgst_amount' => $sgstAmount,
                'igst_amount' => $igstAmount,

                'grand_total' => $validated['grand_total'],
                'created_by' => auth()->id(),
            ]);

            /* ----------------------------------------------------
               INSERT PO ITEMS
            ---------------------------------------------------- */
            foreach ($validated['items']['product_name'] as $index => $name) {

                $qty = $validated['items']['quantity'][$index];
                $rate = $validated['items']['unit_price'][$index];
                $base = $qty * $rate;

                $po->items()->create([
                    'product_id' => $request->items['product_id'][$index] ?? null,
                    'product_name' => $name,
                    'product_description' => $validated['items']['product_description'][$index] ?? null,
                    'hsn_code' => $validated['items']['hsn_code'][$index] ?? null,

                    'quantity' => $qty,
                    'unit_price' => $rate,
                    'uom' => $validated['items']['uom'][$index] ?? null,

                    'total' => $base,
                    'tax_rate' => $validated['items']['tax_rate'][$index],
                    'tax_amount' => $validated['items']['tax_amount'][$index],
                    'total_with_tax' => $validated['items']['total_with_tax'][$index],
                ]);
            }

            /* ----------------------------------------------------
               ACTIVITY LOG
            ---------------------------------------------------- */
            activity()
                ->causedBy(auth()->user())
                ->performedOn($po)
                ->withProperties([
                    'total_items' => $po->items()->count(),
                ])
                ->log("Purchase Order {$po->po_number} created");

            return redirect()
                ->route('purchase-orders.show', $po->id)
                ->with('success', "Purchase Order {$po->po_number} created successfully!");

        } catch (\Throwable $th) {
            report($th);
            return back()->withErrors('Something went wrong while saving PO');
        }
    }



    /* ----------------------------------------------------------
     * UPDATE PO + ITEMS
     * ---------------------------------------------------------- */
    public function update(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::with('items')->findOrFail($id);

        // ❌ Block editing after approval / receiving
        if (in_array($purchaseOrder->status, ['approved', 'received'])) {
            return back()->with('error', 'Approved or Received Purchase Orders cannot be edited.');
        }

        try {

            /* ----------------------------------------------------
               VALIDATION (SAME AS STORE, EXCEPT PO NUMBER)
            ---------------------------------------------------- */
            $validated = $request->validate([
                'supplier_id' => 'required|exists:suppliers,id',
                'ordered_date' => 'nullable|date',
                'delivery_date' => 'nullable|date',
                'po_type' => 'required|in:raw,ready',
                'quote_ref' => 'required|string',

                'remarks' => 'nullable|string',
                'deliver_to_id' => 'required|exists:addresses,id',
                'deliver_to_entity_id' => 'required',
                'notes' => 'nullable|string',
                'tnc' => 'nullable|string',

                'subtotal' => 'required|numeric|min:0',
                'tax_type' => 'required|in:cgst_sgst,igst',
                'grand_total' => 'required|numeric|min:0',

                // Items
                'items.product_name.*' => 'required|string',
                'items.product_description.*' => 'nullable|string',
                'items.hsn_code.*' => 'nullable|string|max:20',
                'items.quantity.*' => 'required|numeric|min:1',
                'items.unit_price.*' => 'required|numeric|min:0',
                'items.uom.*' => 'nullable|string',
                'items.tax_rate.*' => 'required|numeric|min:0',
                'items.tax_amount.*' => 'required|numeric|min:0',
                'items.total_with_tax.*' => 'required|numeric|min:0',
            ]);

            DB::transaction(function () use ($validated, $request, $purchaseOrder) {

                /* ----------------------------------------------------
                   1️⃣ TAX CALCULATION (FROM ITEMS — SAME AS STORE)
                ---------------------------------------------------- */
                $totalTax = array_sum($validated['items']['tax_amount']);

                $cgstAmount = 0;
                $sgstAmount = 0;
                $igstAmount = 0;

                if ($validated['tax_type'] === 'cgst_sgst') {
                    $cgstAmount = $totalTax / 2;
                    $sgstAmount = $totalTax / 2;
                } else {
                    $igstAmount = $totalTax;
                }

                /* ----------------------------------------------------
                   2️⃣ UPDATE PURCHASE ORDER
                ---------------------------------------------------- */
                $purchaseOrder->update([
                    'supplier_id' => $validated['supplier_id'],
                    'ordered_date' => $validated['ordered_date'],
                    'delivery_date' => $validated['delivery_date'],
                    'po_type' => $validated['po_type'],
                    'quote_ref' => $validated['quote_ref'],

                    'remarks' => $validated['remarks'],
                    'deliver_to_id' => $validated['deliver_to_id'],
                    'deliver_to_entity_id' => $validated['deliver_to_entity_id'],
                    'notes' => $validated['notes'],
                    'tnc' => $validated['tnc'],

                    'subtotal' => $validated['subtotal'],
                    'tax_type' => $validated['tax_type'],

                    // Rates intentionally kept 0 (calculated via items)
                    'cgst_rate' => 0,
                    'sgst_rate' => 0,
                    'igst_rate' => 0,

                    'cgst_amount' => $cgstAmount,
                    'sgst_amount' => $sgstAmount,
                    'igst_amount' => $igstAmount,

                    'grand_total' => $validated['grand_total'],
                ]);

                /* ----------------------------------------------------
                   3️⃣ REPLACE ITEMS (SAFE & CLEAN)
                ---------------------------------------------------- */
                $purchaseOrder->items()->delete();

                foreach ($validated['items']['product_name'] as $index => $name) {

                    $qty = $validated['items']['quantity'][$index];
                    $rate = $validated['items']['unit_price'][$index];
                    $base = $qty * $rate;

                    $purchaseOrder->items()->create([
                        'product_id' => $request->items['product_id'][$index] ?? null,
                        'product_name' => $name,
                        'product_description' => $validated['items']['product_description'][$index] ?? null,
                        'hsn_code' => $validated['items']['hsn_code'][$index] ?? null,

                        'quantity' => $qty,
                        'unit_price' => $rate,
                        'uom' => $validated['items']['uom'][$index] ?? null,

                        'total' => $base,
                        'tax_rate' => $validated['items']['tax_rate'][$index],
                        'tax_amount' => $validated['items']['tax_amount'][$index],
                        'total_with_tax' => $validated['items']['total_with_tax'][$index],
                    ]);
                }
            });

            /* ----------------------------------------------------
               ACTIVITY LOG
            ---------------------------------------------------- */
            activity()
                ->causedBy(auth()->user())
                ->performedOn($purchaseOrder)
                ->withProperties([
                    'total_items' => $purchaseOrder->items()->count(),
                ])
                ->log("Purchase Order {$purchaseOrder->po_number} updated");

            return redirect()
                ->route('purchase-orders.show', $purchaseOrder->id)
                ->with('success', "Purchase Order {$purchaseOrder->po_number} updated successfully!");

        } catch (\Throwable $th) {
            report($th);
            return back()->withErrors('Something went wrong while updating PO');
        }
    }



    public function approve($id)
    {
        $purchaseOrder = PurchaseOrder::with('items')->findOrFail($id);

        /* --------------------------------
         * 1️⃣ Validation checks
         * -------------------------------*/
        if ($purchaseOrder->status !== 'draft') {
            return redirect()
                ->back()
                ->with('error', 'Only Draft Purchase Orders can be approved.');
        }

        if ($purchaseOrder->items->count() === 0) {
            return redirect()
                ->back()
                ->with('error', 'Cannot approve PO without items.');
        }

        DB::transaction(function () use ($purchaseOrder) {

            /* --------------------------------
             * 2️⃣ Approve PO
             * -------------------------------*/
            $purchaseOrder->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_on' => Carbon::now(),
            ]);
        });

        /* --------------------------------
         * 3️⃣ Activity Log
         * -------------------------------*/
        activity()
            ->causedBy(auth()->user())
            ->performedOn($purchaseOrder)
            ->withProperties([
                'approved_by' => auth()->user()->name,
                'po_number' => $purchaseOrder->po_number,
            ])
            ->log("Purchase Order {$purchaseOrder->po_number} approved");

        return redirect()
            ->route('purchase-orders.show', $purchaseOrder->id)
            ->with('success', 'Purchase Order approved successfully.');
    }




    /* ----------------------------------------------------------
     * SHOW PAGE
     * ---------------------------------------------------------- */
    public function show($id)
    {
        $purchaseOrder = PurchaseOrder::with('supplier', 'items', 'activityLogs', 'communications', 'grns.items', 'grns.receiver')->findOrFail($id);
        $suppliers = Supplier::all();
        $clients = \App\Models\Client::all();
        $products = Product::all();
        $company = Company::firstOrFail();
        return view('purchase_orders.show', compact('purchaseOrder', 'suppliers', 'company', 'products', 'clients'));
    }



    public function storeInventoryAndReceive(Request $request, $id)
    {
        $request->validate([
            'items.*.purchase_order_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_received' => 'nullable|numeric|min:0.01',
            'items.*.location' => 'nullable|string|max:100',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.supplier_serials' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {

                $purchaseOrder = PurchaseOrder::with([
                    'items',
                    'grns.items',
                ])
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($purchaseOrder->status === 'received') {
                    throw new \Exception('Purchase Order already fully received.');
                }

                // ✅ Create GRN
                $grn = Grn::create([
                    'grn_number' => Grn::generateNumber(),
                    'purchase_order_id' => $purchaseOrder->id,
                    'received_date' => now(),
                    'received_by' => auth()->id(),
                    'remarks' => $request->remarks,
                ]);

                foreach ($request->items as $row) {

                    $qty = (float) ($row['quantity_received'] ?? 0);

                    // ⏭ Skip zero qty rows (RAW + READY both untouched)
                    if ($qty <= 0) {
                        continue;
                    }

                    // 🔑 Resolve PO Item
                    $poItem = $purchaseOrder->items
                        ->firstWhere('id', $row['purchase_order_item_id']);

                    if (!$poItem) {
                        throw new \Exception('Purchase Order item not found.');
                    }

                    // 🔑 Product mapping from GRN (READY only)
                    $productIdFromGrn = $row['product_id'] ?? null;

                    if (
                        $purchaseOrder->po_type === 'ready'
                        && !$productIdFromGrn
                    ) {
                        throw new \Exception(
                            "Product mapping is required for item: {$poItem->product_name}"
                        );
                    }

                    // ✅ Already received qty
                    $alreadyReceived = $purchaseOrder->grns
                        ->flatMap->items
                        ->where('purchase_order_item_id', $poItem->id)
                        ->sum('quantity_received');

                    // ❌ Over-receive protection
                    if (($alreadyReceived + $qty) > $poItem->quantity) {
                        throw new \Exception(
                            "Receiving {$qty} exceeds ordered quantity for {$poItem->product_name}.
                     Ordered: {$poItem->quantity}, Already received: {$alreadyReceived}"
                        );
                    }

                    // ✅ Inventory resolve (RAW untouched, READY mapped)
                    $inventory = Inventory::firstOrCreate(
                        [
                            'inventory_type' => $purchaseOrder->po_type === 'raw'
                                ? 'raw'
                                : 'ready',

                            'product_id' => $purchaseOrder->po_type === 'ready'
                                ? $productIdFromGrn
                                : null,

                            'material_name' => $purchaseOrder->po_type === 'raw'
                                ? $poItem->product_name
                                : null,
                        ],
                        [
                            'uom' => $poItem->uom ?? 'pcs',
                            'location' => $row['location'] ?? 'Main Warehouse',
                            'quantity_available' => 0,
                            'quantity_reserved' => 0,
                        ]
                    );

                    // ✅ Stock update
                    $this->inventoryService->addStock(
                        inventory: $inventory,
                        quantity: $qty,
                        movementType: 'grn',
                        referenceType: Grn::class,
                        referenceId: $grn->id,
                        remarks: "GRN {$grn->grn_number}"
                    );

                    // // ✅ GRN Item snapshot
                    // $grn->items()->create([
                    //     'purchase_order_item_id' => $poItem->id,
                    //     'product_id' => $productIdFromGrn,

                    //     // 🔑 Snapshot for RAW only
                    //     'material_name' => $purchaseOrder->po_type === 'raw'
                    //         ? $poItem->product_name
                    //         : null,

                    //     'quantity_received' => $qty,
                    //     'location' => $row['location'] ?? null,
                    // ]);
                    $grnItem = $grn->items()->create([
                        'purchase_order_item_id' => $poItem->id,
                        'product_id' => $productIdFromGrn,
                        'material_name' => $purchaseOrder->po_type === 'raw'
                            ? $poItem->product_name
                            : null,
                        'quantity_received' => $qty,
                        'location' => $row['location'] ?? null,
                    ]);

                    // 🔐 SUPPLIER SERIAL STORAGE (READY only)
                    if (
                        $purchaseOrder->po_type === 'ready'
                        && !empty($row['supplier_serials'])
                    ) {
                        $serials = preg_split(
                            "/\r\n|\n|\r/",
                            trim($row['supplier_serials'])
                        );

                        if (count($serials) != $qty) {
                            throw new \Exception(
                                "Serial count does not match received quantity for {$poItem->product_name}"
                            );
                        }

                        foreach ($serials as $serial) {
                            GrnItemSerial::create([
                                'grn_item_id' => $grnItem->id,
                                'inventory_id' => $inventory->id,
                                'supplier_serial' => trim($serial),
                            ]);
                        }
                    }

                }

                // 🔄 Refresh relations
                $purchaseOrder->load('grns.items');

                // ✅ Auto-close or partial
                if ($purchaseOrder->isFullyReceived()) {
                    $purchaseOrder->update([
                        'status' => 'received',
                        'received_date' => now(),
                        'received_by' => auth()->id(),
                    ]);
                } else {
                    $purchaseOrder->update([
                        'status' => 'partial',
                        'received_by' => auth()->id(),
                    ]);
                }

                activity()
                    ->performedOn($purchaseOrder)
                    ->causedBy(auth()->user())
                    ->log("GRN {$grn->grn_number} generated");
            });
            return redirect()
                ->route('purchase-orders.show', $id)
                ->with('success', 'GRN generated and inventory updated.');
        } catch (\Throwable $th) {
            report($th);
            return back()->withErrors($th->getMessage());
        }
    }







    /* ----------------------------------------------------------
     * PDF PRINT
     * ---------------------------------------------------------- */

    public function printPO($id)
    {
        $po = PurchaseOrder::with(['supplier', 'items'])->findOrFail($id);

        $pdfBinary = $this->pdfService->generate($po);

        return response($pdfBinary)
            ->header('Content-Type', 'application/pdf');
    }



    public function sendMail(Request $request, $id)
    {
        $request->validate([
            'from_email' => 'required|email',
            'to' => 'required',
            'subject' => 'required',
            'body' => 'required'
        ]);

        $purchaseOrder = PurchaseOrder::findOrFail($id);

        try {
            $mailer = str_contains($request->from_email, 'sales')
                ? 'sales'
                : 'info';

            // dd($mailer);

            Mail::mailer($mailer)
                ->to(explode(',', $request->to))
                ->cc($request->cc ? explode(',', $request->cc) : [])
                ->send(new PurchaseOrderMail(
                    $purchaseOrder->id,
                    $request->only('from_email', 'subject', 'body')
                ));

        } catch (\Throwable $e) {

            Log::error('PO Mail Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', $e->getMessage());
        }

        Communication::create([
            'model_type' => PurchaseOrder::class,
            'model_id' => $purchaseOrder->id,
            'from_email' => $request->from_email,
            'to_emails' => explode(',', $request->to),
            'cc_emails' => $request->cc ? explode(',', $request->cc) : [],
            'subject' => $request->subject,
            'body' => $request->body,
            'sent_by' => auth()->id(),
            'sent_at' => now(),
            'status' => 'sent'
        ]);

        return back()->with('success', 'Purchase Order emailed successfully');
    }




}
