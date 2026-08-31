<?php

namespace App\Http\Controllers;

use App\Mail\SalesQuotationMail;
use App\Models\Communication;
use App\Models\SalesInvoice;
use App\Models\SalesProforma;
use App\Services\SalesItemsCalculationService;
use App\Services\SalesQuotationPdfService;
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

class SalesQuotationController extends Controller
{


    protected SalesQuotationPdfService $pdfService;

    public function __construct(SalesQuotationPdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function index()
    {
        $clients = Client::with('billingAddress')->orderBy('name')->get();
        $products = Product::orderBy('our_part_no')->get();
        
        $companyBranch = \App\Models\CompanyBranch::getDefault();
        $branchState = $companyBranch ? $companyBranch->state : '';

        return view('sales-order.quotation.index', compact('products', 'clients', 'branchState'));
    }



    public function store(Request $request, SalesItemsCalculationService $calculator)
    {

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'quotation_number' => 'required|string|unique:sales_quotations,quotation_number',
            'quotation_date' => 'required|date',
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
         | Create Quotation
         ------------------------------ */
        $quotation = SalesQuotation::create([
            'client_id' => $validated['client_id'],

            'quotation_number' => $validated['quotation_number'],
            'quotation_date' => $validated['quotation_date'],
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
         | Insert Quotation Items
         ------------------------------ */
        foreach ($calculatedItems as $item) {
            $quotation->items()->create([
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
            ->performedOn($quotation)
            ->withProperties([
                'items' => $quotation->items()->count(),
                'total' => $quotation->grand_total,
            ])
            ->log("Quotation {$quotation->quotation_number} created");

        return redirect()
            ->route('quotations.show', $quotation->id)
            ->with('success', "Quotation {$quotation->quotation_number} created successfully!");
    }


    public function show($id)
    {
        $order = SalesQuotation::with([
            'client',
            'items.product',
            'creator',
            'proforma',
            'invoice',
            'activityLogs' // If using Spatie Activity Log morph relation
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

        return view('sales-order.quotation.show', compact('order', 'qrSvg', 'products', 'clients'));
    }

    public function getQuotationsDataTable(Request $request)
    {
        if ($request->ajax()) {

            $data = SalesQuotation::query()
                ->select([
                    'sales_quotations.id',
                    'sales_quotations.quotation_number',
                    'clients.name as client',
                    'sales_quotations.quotation_date',
                    'sales_quotations.status',
                    'sales_quotations.grand_total',
                    'sales_quotations.created_at',
                ])
                ->leftJoin('clients', 'sales_quotations.client_id', '=', 'clients.id');

            return DataTables::of($data)

                // 🔍 Search by client name
                ->filterColumn('client', function ($query, $keyword) {
                    $query->where('clients.name', 'like', "%{$keyword}%");
                })

                // 🔍 Search by quotation number
                ->filterColumn('quotation_number', function ($query, $keyword) {
                    $query->where('sales_quotation.quotation_number', 'like', "%{$keyword}%");
                })

                // 🧾 Status badge (optional but recommended)
                ->editColumn('status', function ($quotation) {
                    return ucfirst($quotation->status);
                })

                // ⚙ Actions
                ->addColumn('actions', function ($quotation) {
                    return '
                    <a href="' . route('quotations.show', $quotation->id) . '" 
                       class="btn btn-sm btn-outline-warning" title="View">
                        <i class="fas fa-eye"></i>
                    </a>
                ';
                })

                ->rawColumns(['actions'])
                ->make(true);
        }
    }

    public function updateItems(Request $request, SalesQuotation $quotation, SalesItemsCalculationService $calculator)
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

        $quotation->items()->delete();

        $items = collect();

        foreach ($validated['items'] as $item) {
            $calc = $calculator->calculateItem($item);
            $items->push(array_merge($item, $calc));
        }

        $totals = $calculator->calculateTotals(
            $items,
            $quotation->tax_type
        );

        foreach ($items as $item) {
            // $quotation->items()->create($item);
            $quotation->items()->create([
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

        $quotation->update([
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
            ->performedOn($quotation)
            ->withProperties([
                'items' => $quotation->items()->count(),
                'total' => $quotation->grand_total,
            ])
            ->log("Quotation {$quotation->quotation_number} items updated.");

        return back()->with('success', 'Quotation items updated successfully.')->withFragment('tab-items');

    }


    public function update(Request $request, SalesQuotation $quotation, SalesItemsCalculationService $calculator)
    {
        /* ------------------------------
         | Validation
         ------------------------------ */
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'quotation_date' => 'required|date',
            'client_query_from' => 'required|string|max:255',
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

        /* ------------------------------
         | Update Quotation
         ------------------------------ */
        $quotation->update([
            'client_id' => $validated['client_id'],
            'quotation_id' => $request->quotation_id,
            'proforma_id' => $request->proforma_id,

            'quotation_date' => $validated['quotation_date'],
            'billing_address_id' => $validated['billing_address_id'],
            'shipping_address_id' => $validated['shipping_address_id'],

            'client_query_from' => $validated['client_query_from'],

            'remarks' => $validated['remarks'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'tnc' => $validated['tnc'] ?? null,

            'tax_type' => $validated['tax_type'],
            'subtotal' => $totals['subtotal'],
            'cgst_amount' => $totals['cgst_amount'],
            'sgst_amount' => $totals['sgst_amount'],
            'igst_amount' => $totals['igst_amount'],
            'grand_total' => $totals['grand_total'],
        ]);

        /* ------------------------------
         | Replace Quotation Items
         ------------------------------ */
        $quotation->items()->delete();

        foreach ($calculatedItems as $item) {

            $quotation->items()->create([
                'sales_quotation_id' => $quotation->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'taxable_amount' => $item['taxable_amount'],
                'tax_rate' => $item['tax_rate'],
                'tax_amount' => $item['tax_amount'],
                'discount_percent' => $item['discount_percent'],
                'discount_amount' => $item['discount_amount'],
                'total_with_tax' => $item['total_with_tax'],
            ]);
        }

        /* ------------------------------
         | Activity Log
         ------------------------------ */
        activity()
            ->causedBy(auth()->user())
            ->performedOn($quotation)
            ->withProperties([
                'items' => $quotation->items()->count(),
                'total' => $quotation->grand_total,
            ])
            ->log("Quotation {$quotation->quotation_number} updated");

        return redirect()
            ->route('quotations.show', $quotation->id)
            ->with('success', "Quotation {$quotation->quotation_number} updated successfully!");
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


    public function generateQuotationNumber()
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
        $lastQuotaion = SalesQuotation::where('quotation_number', 'like', "%{$financialYear}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastQuotaion) {
            $lastIncrement = (int) substr($lastQuotaion->quotation_number, -4);
            $newIncrement = str_pad($lastIncrement + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newIncrement = '0001';
        }

        return response()->json([
            'quotation_number' => "{$prefix}-RFQ-{$financialYear}-{$newIncrement}",
        ]);
    }


    public function printQuotation($id)
    {
        // dd("Hello");
        $so = SalesQuotation::with(['client', 'items'])->findOrFail($id);

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

        $salesQuotation = SalesQuotation::findOrFail($id);

        try {
            $mailer = str_contains($request->from_email, 'sales')
                ? 'sales'
                : 'info';

            // dd($mailer);

            Mail::mailer($mailer)
                ->to(explode(',', $request->to))
                ->cc($request->cc ? explode(',', $request->cc) : [])
                ->send(new SalesQuotationMail(
                    $salesQuotation->id,
                    $request->only('from_email', 'subject', 'body')
                ));

        } catch (\Throwable $e) {

            Log::error('Quotation Mail Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', $e->getMessage());
        }

        Communication::create([
            'model_type' => SalesQuotation::class,
            'model_id' => $salesQuotation->id,
            'from_email' => $request->from_email,
            'to_emails' => explode(',', $request->to),
            'cc_emails' => $request->cc ? explode(',', $request->cc) : [],
            'subject' => $request->subject,
            'body' => $request->body,
            'sent_by' => auth()->id(),
            'sent_at' => now(),
            'status' => 'sent'
        ]);

        $salesQuotation->update(['status' => 'sent']);

        // Log activity
        activity()->causedBy(auth()->user())->performedOn($salesQuotation)
            ->log("Quotation sent to customer via mail");

        return back()->with('success', 'Quotation emailed successfully');
    }



    public function accept(SalesQuotation $quotation)
    {
        if ($quotation->status === 'accepted') {
            return back()->with('error', 'Quotation already accepted.');
        }

        $quotation->update(['status' => 'accepted']);

        // Log activity
        activity()->causedBy(auth()->user())->performedOn($quotation)
            ->log("Quotation marked as Accepted");

        return back()->with('success', 'Quotation accepted successfully.');
    }






}
