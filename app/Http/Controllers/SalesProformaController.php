<?php

namespace App\Http\Controllers;

use App\Mail\SalesProformaMail;
use App\Mail\SalesQuotationMail;
use App\Models\Communication;
use App\Models\SalesProforma;
use App\Services\SalesItemsCalculationService;
use App\Services\SalesProformaPdfService;
use DB;
use Illuminate\Http\Request;
use Log;
use Mail;
use Yajra\DataTables\DataTables;
use App\Models\SalesQuotation;
use App\Models\Product;
use App\Models\Client;
use App\Models\Company;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Collection;

class SalesProformaController extends Controller
{


    protected SalesProformaPdfService $pdfService;

    public function __construct(SalesProformaPdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function index()
    {
        $clients = Client::with('billingAddress')->orderBy('name')->get();
        $products = Product::orderBy('our_part_no')->get();

        $companyBranch = \App\Models\CompanyBranch::getDefault();
        $branchState = $companyBranch ? $companyBranch->state : '';

        return view('sales-order.proforma.index', compact('products', 'clients', 'branchState'));
    }



    public function store(Request $request, SalesItemsCalculationService $calculator)
    {

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'proforma_number' => 'required|string|unique:sales_proformas,proforma_number',
            'proforma_date' => 'required|date',
            'status' => 'required|in:draft,issued,paid,cancelled',
            'client_query_from' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'notes' => 'nullable|string',
            'tnc' => 'nullable|string',
            'tax_type' => 'required|in:cgst_sgst,igst',
            'billing_address_id' => 'required|exists:addresses,id',
            'shipping_address_id' => 'required|exists:addresses,id',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);




        /* ------------------------------
         | Calculate Items
         ------------------------------ */
        $calculatedItems = collect();

        foreach ($validated['items'] as $item) {
            $calc = $calculator->calculateItem($item);

            $calculatedItems->push(array_merge($item, $calc));
        }

        /* ------------------------------
         | Calculate Totals
         ------------------------------ */
        $totals = $calculator->calculateTotals(
            $calculatedItems,
            $validated['tax_type']
        );

        // dd($calculatedItems);

        /* ------------------------------
         | Create Proforma
         ------------------------------ */
        $proforma = SalesProforma::create([
            'client_id' => $validated['client_id'],

            'proforma_number' => $validated['proforma_number'],
            'proforma_date' => $validated['proforma_date'],
            'billing_address_id' => $validated['billing_address_id'],
            'shipping_address_id' => $validated['shipping_address_id'],
            'status' => $validated['status'],

            'client_query_from' => $validated['client_query_from'],

            'tax_type' => $validated['tax_type'],

            'remarks' => $validated['remarks'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'tnc' => $validated['tnc'] ?? null,

            'subtotal' => $totals['subtotal'],
            'cgst_amount' => $totals['cgst_amount'],
            'sgst_amount' => $totals['sgst_amount'],
            'igst_amount' => $totals['igst_amount'],
            'grand_total' => $totals['grand_total'],

            'created_by' => auth()->id(),
        ]);

        /* ------------------------------
         | Insert Proforma Items
         ------------------------------ */
        foreach ($calculatedItems as $item) {
            $proforma->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],

                'discount_percent' => $item['discount_percent'] ?? 0,
                'discount_amount' => $item['discount_amount'] ?? 0,
                'taxable_amount' => $item['taxable_amount'],
                'tax_rate' => $item['tax_rate'],
                'tax_amount' => $item['tax_amount'],
                'total_with_tax' => $item['total_with_tax'],
            ]);
        }

        /* ------------------------------
         | Activity Log
         ------------------------------ */
        activity()
            ->causedBy(auth()->user())
            ->performedOn($proforma)
            ->withProperties([
                'items' => $proforma->items()->count(),
                'total' => $proforma->grand_total,
            ])
            ->log("Proforma Invoice {$proforma->proforma_number} created");

        return redirect()
            ->route('proformas.show', $proforma->id)
            ->with('success', "Proforma Invoice {$proforma->proforma_number} created successfully!");
    }


    public function show($id)
    {
        $order = SalesProforma::with([
            'client',
            'items.product',
            'creator',
            'quotation',
            'invoice',
            'activityLogs'
        ])->findOrFail($id);

        $products = Product::orderBy('our_part_no')->get();
        $clients = Client::orderBy('name')->get();

        // Generate QR code using chillerlan/qrcode
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel' => QRCode::ECC_L,
            'scale' => 4,
        ]);

        // Create QR code containing Order Number
        $rawSvg = (new QRCode($options))->render($order->order_no);
        $qrSvg = html_entity_decode($rawSvg);

        return view('sales-order.proforma.show', compact('order', 'qrSvg', 'products', 'clients'));
    }

    public function getProformasDataTable(Request $request)
    {
        if ($request->ajax()) {

            $data = SalesProforma::query()
                ->select([
                    'sales_proformas.id',
                    'sales_proformas.proforma_number',
                    'clients.name as client',
                    'sales_proformas.proforma_date',
                    'sales_proformas.status',
                    'sales_proformas.grand_total',
                    'sales_proformas.created_at',
                ])
                ->leftJoin('clients', 'sales_proformas.client_id', '=', 'clients.id');

            return DataTables::of($data)

                // 🔍 Search by client name
                ->filterColumn('client', function ($query, $keyword) {
                    $query->where('clients.name', 'like', "%{$keyword}%");
                })

                // 🔍 Search by proforma number
                ->filterColumn('proforma_number', function ($query, $keyword) {
                    $query->where('sales_proforma.proforma_number', 'like', "%{$keyword}%");
                })

                // 🧾 Status badge (optional but recommended)
                ->editColumn('status', function ($proforma) {
                    return ucfirst($proforma->status);
                })

                // ⚙ Actions
                ->addColumn('actions', function ($proforma) {
                    return '
                    <a href="' . route('proformas.show', $proforma->id) . '" 
                       class="btn btn-sm btn-outline-warning" title="View">
                        <i class="fas fa-eye"></i>
                    </a>
                ';
                })

                ->rawColumns(['actions'])
                ->make(true);
        }
    }

    public function updateItems(Request $request, SalesProforma $proforma, SalesItemsCalculationService $calculator)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
        ]);

        $proforma->items()->delete();

        $items = collect();

        foreach ($validated['items'] as $item) {
            $calc = $calculator->calculateItem($item);
            $items->push(array_merge($item, $calc));
        }

        $totals = $calculator->calculateTotals(
            $items,
            $proforma->tax_type
        );

        foreach ($items as $item) {
            // $proforma->items()->create($item);
            $proforma->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_percent' => $item['discount_percent'] ?? 0,
                'discount_amount' => $item['discount_amount'] ?? 0,
                'taxable_amount' => $item['taxable_amount'],
                'tax_rate' => $item['tax_rate'],
                'tax_amount' => $item['tax_amount'],
                'total_with_tax' => $item['total_with_tax'],
            ]);
        }

        $proforma->update([
            'subtotal' => $totals['subtotal'],
            'cgst_amount' => $totals['cgst_amount'],
            'sgst_amount' => $totals['sgst_amount'],
            'igst_amount' => $totals['igst_amount'],
            'grand_total' => $totals['grand_total'],
        ]);

        /* ------------------------------
         | Activity Log
         ------------------------------ */
        activity()
            ->causedBy(auth()->user())
            ->performedOn($proforma)
            ->withProperties([
                'items' => $proforma->items()->count(),
                'total' => $proforma->grand_total,
            ])
            ->log("PI {$proforma->proforma_number} items updated.");

        return back()->with('success', 'PI items updated successfully.')->withFragment('tab-items');

    }


    public function update(Request $request, SalesProforma $proforma)
    {
        /* ------------------------------
         | Validation
         ------------------------------ */
        $validated = $request->validate([
            'proforma_date' => 'required|date',
            'client_query_from' => 'required|string|max:255',
            'remarks' => 'nullable|string',
            'notes' => 'nullable|string',
            'tnc' => 'nullable|string',
            'tax_type' => 'required|in:cgst_sgst,igst',
            'billing_address_id' => 'required|exists:addresses,id',
            'shipping_address_id' => 'required|exists:addresses,id',

        ]);


        /* ------------------------------
         | Update Proforma
         ------------------------------ */
        $proforma->update([
            'proforma_date' => $validated['proforma_date'],

            'client_query_from' => $validated['client_query_from'],

            'remarks' => $validated['remarks'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'tnc' => $validated['tnc'] ?? null,

            'tax_type' => $validated['tax_type'],
            'billing_address_id' => $validated['billing_address_id'],
            'shipping_address_id' => $validated['shipping_address_id'],
        ]);

        /* ------------------------------
         | Activity Log
         ------------------------------ */
        activity()
            ->causedBy(auth()->user())
            ->performedOn($proforma)
            ->log("{$proforma->proforma_number} PI Details updated");

        return redirect()
            ->route('proformas.show', $proforma->id)
            ->with('success', "{$proforma->proforma_number} Proforma Details  updated successfully!");
    }


    public function updateStatus(Request $request, $id)
    {
        $order = SalesQuotation::findOrFail($id);

        // Validate the status
        $validated = $request->validate([
            'status' => 'required|string|in:Draft,Confirmed,In-Progress,Shipped,Cancelled,Delivered',
        ]);

        $order->status = $validated['status'];
        $order->save();

        // Log activity
        activity()->causedBy(auth()->user())->performedOn($order)
            ->log("Order marked as {$validated['status']}");

        return back()->with('success', "Order marked as {$validated['status']}.");
    }


    public function generateProformaNumber()
    {
        $company = Company::firstOrFail();
        $prefix = $company->company_code;

        $now = now();
        $year = $now->year;
        $month = $now->month;

        // ===== Financial Year Calculation =====
        if ($month >= 4) {
            $fyStart = substr($year, -2);
            $fyEnd = substr($year + 1, -2);
        } else {
            $fyStart = substr($year - 1, -2);
            $fyEnd = substr($year, -2);
        }

        $financialYear = "{$fyStart}-{$fyEnd}";

        // ===== Get last Quotation of SAME Financial Year =====
        $lastProforma = SalesProforma::where('proforma_number', 'like', "%{$financialYear}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastProforma) {
            $lastIncrement = (int) substr($lastProforma->proforma_number, -4);
            $newIncrement = str_pad($lastIncrement + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newIncrement = '0001';
        }

        return response()->json([
            'proforma_number' => "{$prefix}-PI-{$financialYear}-{$newIncrement}",
        ]);
    }


    public function printProforma($id)
    {
        // dd("Hello");
        $so = SalesProforma::with(['client', 'items'])->findOrFail($id);

        $pdfBinary = $this->pdfService->generate($so);

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

        $salesProforma = SalesProforma::findOrFail($id);

        try {
            $mailer = str_contains($request->from_email, 'sales')
                ? 'sales'
                : 'info';

            // dd($mailer);

            Mail::mailer($mailer)
                ->to(explode(',', $request->to))
                ->cc($request->cc ? explode(',', $request->cc) : [])
                ->send(new SalesProformaMail(
                    $salesProforma->id,
                    $request->only('from_email', 'subject', 'body')
                ));

        } catch (\Throwable $e) {

            Log::error('PI Mail Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', $e->getMessage());
        }

        Communication::create([
            'model_type' => SalesProforma::class,
            'model_id' => $salesProforma->id,
            'from_email' => $request->from_email,
            'to_emails' => explode(',', $request->to),
            'cc_emails' => $request->cc ? explode(',', $request->cc) : [],
            'subject' => $request->subject,
            'body' => $request->body,
            'sent_by' => auth()->id(),
            'sent_at' => now(),
            'status' => 'sent'
        ]);

        $salesProforma->update(['status' => 'sent']);

        // Log activity
        activity()->causedBy(auth()->user())->performedOn($salesProforma)
            ->log("PI sent to customer via mail");

        return back()->with('success', 'PI emailed successfully');
    }



    public function accept(SalesProforma $proforma)
    {
        if ($proforma->status === 'accepted') {
            return back()->with('error', 'PI already accepted.');
        }

        $proforma->update(['status' => 'accepted']);

        // Log activity
        activity()->causedBy(auth()->user())->performedOn($proforma)
            ->log("PI marked as Accepted");

        return back()->with('success', 'PI accepted successfully.');
    }


    public function storeFromQuotation(Request $request, SalesQuotation $quotation, SalesItemsCalculationService $calculator)
    {
        // 1️⃣ Quotation status check
        if ($quotation->status !== 'accepted') {
            return back()->with('error', 'Quotation must be accepted first.');
        }

        if ($quotation->invoice) {
            return back()->with('error', 'Invoice already created.');
        }

        if ($quotation->proforma) {
            return back()->with('error', 'Proforma Invoice already created.');
        }

        // 2️⃣ Validate mandatory PO fields
        $validated = $request->validate([
            'client_po_ref' => 'required|string|max:255',
            'client_po_pdf' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        // 3️⃣ Upload PO PDF IF AVAILABLE
        $clientPoPdfPath = null;
        if ($request->hasFile('client_po_pdf')) {
            $clientPoPdfPath = $request->file('client_po_pdf')
                ->store('client_po_pdfs', 'public');
        }

        // 4️⃣ Create Invoice inside transaction
        $proforma = DB::transaction(function () use ($quotation, $validated, $clientPoPdfPath) {

            $proforma = SalesProforma::create([
                'client_id' => $quotation->client_id,
                'quotation_id' => $quotation->id,
                'proforma_number' => $this->generateProformaNumber()->getData()->proforma_number,
                'proforma_date' => now(),
                'status' => 'issued',

                // 🔑 FROM FORM
                'client_po_ref' => $validated['client_po_ref'],
                'client_po_pdf' => $clientPoPdfPath,

                'tax_type' => $quotation->tax_type,
                'subtotal' => $quotation->subtotal,
                'cgst_amount' => $quotation->cgst_amount,
                'sgst_amount' => $quotation->sgst_amount,
                'igst_amount' => $quotation->igst_amount,
                'grand_total' => $quotation->grand_total,
                'created_by' => auth()->id(),
            ]);

            // Copy items
            foreach ($quotation->items as $item) {
                $quantity = $item->quantity;

                $proforma->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $quantity,

                    // Discount baked into price
                    'unit_price' => $item->unit_price,
                    'discount_percent' => $item->discount_percent,
                    'discount_amount' => $item->discount_amount,

                    // Clean totals
                    'taxable_amount' => $item->taxable_amount,
                    'tax_rate' => $item->tax_rate,
                    'tax_amount' => $item->tax_amount,
                    'total_with_tax' => $item->total_with_tax,
                ]);

            }

            // Update quotation status
            $quotation->update(['status' => 'converted']);

            return $proforma;
        });


        return redirect()
            ->route('proformas.show', $proforma->id)
            ->with('success', 'Proforma Invoice created successfully from quotation.');
    }






}
