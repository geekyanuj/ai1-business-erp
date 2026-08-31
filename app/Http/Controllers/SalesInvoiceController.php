<?php

namespace App\Http\Controllers;

use App\Mail\SalesInvoiceMail;
use App\Models\Communication;
use App\Models\SalesProforma;
use App\Models\SalesQuotation;
use App\Services\SalesInvoicePdfService;
use DB;
use Illuminate\Http\Request;
use Log;
use Mail;
use Yajra\DataTables\DataTables;
use App\Models\SalesInvoice;
use App\Models\Product;
use App\Models\Client;
use App\Models\Company;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use App\Services\SalesItemsCalculationService;
use Illuminate\Support\Collection;

class SalesInvoiceController extends Controller
{


    protected SalesInvoicePdfService $pdfService;

    public function __construct(SalesInvoicePdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function index()
    {
        $clients = Client::with('billingAddress')->orderBy('name')->get();
        $products = Product::orderBy('our_part_no')->get();

        $companyBranch = \App\Models\CompanyBranch::getDefault();
        $branchState = $companyBranch ? $companyBranch->state : '';

        return view('sales-order.invoice.index', compact('products', 'clients', 'branchState'));
    }



    public function store(Request $request, SalesItemsCalculationService $calculator)
    {

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => 'required|string|unique:sales_invoices,invoice_number',
            'invoice_date' => 'required|date',
            'status' => 'required|in:draft,issued,paid,cancelled',
            'payment_mode' => 'nullable|string|max:50',
            'client_po_ref' => 'nullable|string|max:255',
            'client_po_pdf' => 'nullable|file|mimes:pdf|max:5120',
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
            'items.*.discount_percent' => 'nullable|numeric|min:0',
        ]);



        /* ------------------------------
         | Upload Client PO PDF
         ------------------------------ */
        $clientPdfPath = null;
        if ($request->hasFile('client_po_pdf')) {
            $clientPdfPath = $request->file('client_po_pdf')
                ->store('client_po_pdfs', 'public');
        }

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
         | Create Invoice
         ------------------------------ */
        $invoice = SalesInvoice::create([
            'client_id' => $validated['client_id'],
            'quotation_id' => $request->quotation_id,
            'proforma_id' => $request->proforma_id,

            'invoice_number' => $validated['invoice_number'],
            'invoice_date' => $validated['invoice_date'],
            'billing_address_id' => $validated['billing_address_id'],
            'shipping_address_id' => $validated['shipping_address_id'],
            'status' => $validated['status'],

            'payment_mode' => $validated['payment_mode'],
            'client_po_ref' => $validated['client_po_ref'],
            'client_po_pdf' => $clientPdfPath,

            'remarks' => $validated['remarks'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'tnc' => $validated['tnc'] ?? null,

            'tax_type' => $validated['tax_type'],
            'subtotal' => $totals['subtotal'],
            'cgst_amount' => $totals['cgst_amount'],
            'sgst_amount' => $totals['sgst_amount'],
            'igst_amount' => $totals['igst_amount'],
            'grand_total' => $totals['grand_total'],

            'created_by' => auth()->id(),
        ]);

        /* ------------------------------
         | Insert Invoice Items
         ------------------------------ */
        foreach ($calculatedItems as $item) {
            $invoice->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
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
            ->performedOn($invoice)
            ->withProperties([
                'items' => $invoice->items()->count(),
                'total' => $invoice->grand_total,
            ])
            ->log("Invoice {$invoice->invoice_number} created");

        return redirect()
            ->route('invoices.show', $invoice->id)
            ->with('success', "Invoice {$invoice->invoice_number} created successfully!");
    }


    public function show($id)
    {
        $order = SalesInvoice::with([
            'client',
            'items.product',
            'creator',
            'proforma',
            'quotation',
            'activityLogs',
            'communications',
            'payments.creator',
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

        return view('sales-order.invoice.show', compact('order', 'qrSvg', 'products', 'clients'));
    }

    public function getInvoicesDataTable(Request $request)
    {
        if ($request->ajax()) {

            $data = SalesInvoice::query()
                ->select([
                    'sales_invoices.id',
                    'sales_invoices.invoice_number',
                    'clients.name as client',
                    'sales_invoices.invoice_date',
                    'sales_invoices.status',
                    'sales_invoices.payment_mode',
                    'sales_invoices.grand_total',
                    'sales_invoices.created_at',
                ])
                ->leftJoin('clients', 'sales_invoices.client_id', '=', 'clients.id');

            return DataTables::of($data)

                // 🔍 Search by client name
                ->filterColumn('client', function ($query, $keyword) {
                    $query->where('clients.name', 'like', "%{$keyword}%");
                })

                // 🔍 Search by invoice number
                ->filterColumn('invoice_number', function ($query, $keyword) {
                    $query->where('sales_invoices.invoice_number', 'like', "%{$keyword}%");
                })

                // 🧾 Status badge (optional but recommended)
                ->editColumn('status', function ($invoice) {
                    return ucfirst($invoice->status);
                })

                // ⚙ Actions
                ->addColumn('actions', function ($invoice) {
                    return '
                    <a href="' . route('invoices.show', $invoice->id) . '" 
                       class="btn btn-sm btn-outline-warning" title="View">
                        <i class="fas fa-eye"></i>
                    </a>
                ';
                })

                ->rawColumns(['actions'])
                ->make(true);
        }
    }

    public function updateItems(Request $request, SalesInvoice $invoice, SalesItemsCalculationService $calculator)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0',
        ]);

        $invoice->items()->delete();

        $items = collect();

        foreach ($validated['items'] as $item) {
            $calc = $calculator->calculateItem($item);
            $items->push(array_merge($item, $calc));
        }

        $totals = $calculator->calculateTotals(
            $items,
            $invoice->tax_type
        );

        foreach ($items as $item) {
            $invoice->items()->create($item);
        }

        $invoice->update([
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
            ->performedOn($invoice)
            ->withProperties([
                'items' => $invoice->items()->count(),
                'total' => $invoice->grand_total,
            ])
            ->log("Invoice {$invoice->invoice_number} items updated.");

        return back()->with('success', 'Invoice items updated successfully.')->withFragment('tab-items');

    }


    public function update(Request $request, SalesInvoice $invoice, SalesItemsCalculationService $calculator)
    {
        /* ------------------------------
         | Validation
         ------------------------------ */
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            // 'status' => 'required|in:draft,issued,paid,cancelled',
            'payment_mode' => 'nullable|string|max:50',
            'client_po_ref' => 'nullable|string|max:255',
            'client_po_pdf' => 'nullable|file|mimes:pdf|max:5120',
            'remarks' => 'nullable|string',
            'notes' => 'nullable|string',
            'tnc' => 'nullable|string',
            'tax_type' => 'required|in:cgst_sgst,igst',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0',
            'billing_address_id' => 'required|exists:addresses,id',
            'shipping_address_id' => 'required|exists:addresses,id',
        ]);

        /* ------------------------------
         | Handle Client PO PDF
         ------------------------------ */
        $clientPdfPath = $invoice->client_po_pdf;

        if ($request->hasFile('client_po_pdf')) {
            // optional: delete old file
            if ($invoice->client_po_pdf) {
                \Storage::disk('public')->delete($invoice->client_po_pdf);
            }

            $clientPdfPath = $request->file('client_po_pdf')
                ->store('client_po_pdfs', 'public');
        }

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
         | Update Invoice
         ------------------------------ */
        $invoice->update([
            'client_id' => $validated['client_id'],
            'quotation_id' => $request->quotation_id,
            'proforma_id' => $request->proforma_id,

            'invoice_date' => $validated['invoice_date'],
            'billing_address_id' => $validated['billing_address_id'],
            'shipping_address_id' => $validated['shipping_address_id'],
            // 'status' => $validated['status'],

            'payment_mode' => $validated['payment_mode'],
            'client_po_ref' => $validated['client_po_ref'],
            'client_po_pdf' => $clientPdfPath,

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
         | Replace Invoice Items
         ------------------------------ */
        $invoice->items()->delete();

        foreach ($calculatedItems as $item) {

            $invoice->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
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
            ->performedOn($invoice)
            ->withProperties([
                'items' => $invoice->items()->count(),
                'total' => $invoice->grand_total,
            ])
            ->log("Invoice {$invoice->invoice_number} updated");

        return redirect()
            ->route('invoices.show', $invoice->id)
            ->with('success', "Invoice {$invoice->invoice_number} updated successfully!");
    }








    public function updateStatus(Request $request, $id)
    {
        $order = SalesInvoice::findOrFail($id);

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


    public function generateInvoiceNumber()
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

        // ===== Get last Invoice of SAME Financial Year =====
        $lastInvoice = SalesInvoice::where('invoice_number', 'like', "%{$financialYear}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastIncrement = (int) substr($lastInvoice->invoice_number, -4);
            $newIncrement = str_pad($lastIncrement + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newIncrement = '0001';
        }

        return response()->json([
            'invoice_number' => "{$prefix}-INV-{$financialYear}-{$newIncrement}",
        ]);
    }


    public function printInvoice($id)
    {
        $so = SalesInvoice::with(['client', 'items'])->findOrFail($id);

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

        $salesInvoice = SalesInvoice::findOrFail($id);

        if ($salesInvoice->status !== 'paid') {
            return back()->with('error', 'Invoice Amount is not paid.');
        }

        try {
            $mailer = str_contains($request->from_email, 'sales')
                ? 'sales'
                : 'info';

            // dd($mailer);

            Mail::mailer($mailer)
                ->to(explode(',', $request->to))
                ->cc($request->cc ? explode(',', $request->cc) : [])
                ->send(new SalesInvoiceMail(
                    $salesInvoice->id,
                    $request->only('from_email', 'subject', 'body')
                ));

        } catch (\Throwable $e) {

            Log::error('Invoice Mail Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', $e->getMessage());
        }

        Communication::create([
            'model_type' => SalesInvoice::class,
            'model_id' => $salesInvoice->id,
            'from_email' => $request->from_email,
            'to_emails' => explode(',', $request->to),
            'cc_emails' => $request->cc ? explode(',', $request->cc) : [],
            'subject' => $request->subject,
            'body' => $request->body,
            'sent_by' => auth()->id(),
            'sent_at' => now(),
            'status' => 'sent'
        ]);


        return back()->with('success', 'Tax Invoice emailed successfully');
    }


    public function storeFromQuotation(Request $request, SalesQuotation $quotation, SalesItemsCalculationService $calculator)
    {
        // 1️⃣ Quotation status check
        if ($quotation->status !== 'accepted') {
            return back()->with('error', 'Quotation must be accepted first.');
            // abort(403, 'Quotation must be accepted first.');
        }

        if ($quotation->invoice) {
            return back()->with('error', 'Invoice already created.');
        }

        // 2️⃣ Validate mandatory PO fields
        $validated = $request->validate([
            'client_po_ref' => 'required|string|max:255',
            'client_po_pdf' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        // 3️⃣ Upload PO PDF
        $clientPoPdfPath = $request->file('client_po_pdf')
            ->store('client_po_pdfs', 'public');

        // 4️⃣ Create Invoice inside transaction
        $invoice = DB::transaction(function () use ($quotation, $validated, $clientPoPdfPath) {

            $invoice = SalesInvoice::create([
                'client_id' => $quotation->client_id,
                'quotation_id' => $quotation->id,
                'invoice_number' => $this->generateInvoiceNumber()->getData()->invoice_number,
                'invoice_date' => now(),
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

                // Effective price AFTER discount
                $effectiveUnitPrice = $quantity > 0
                    ? round($item->taxable_amount / $quantity, 2)
                    : 0;

                $invoice->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $quantity,

                    // Discount baked into price
                    'unit_price' => $effectiveUnitPrice,

                    // Clean totals
                    'taxable_amount' => $item->taxable_amount,
                    'tax_rate' => $item->tax_rate,
                    'tax_amount' => $item->tax_amount,
                    'total_with_tax' => $item->total_with_tax,
                ]);

            }

            // Update quotation status
            $quotation->update(['status' => 'converted']);

            return $invoice;
        });


        return redirect()
            ->route('invoices.show', $invoice->id)
            ->with('success', 'Invoice created successfully from quotation.');
    }

    public function storeFromProforma(Request $request, SalesProforma $proforma, SalesItemsCalculationService $calculator)
    {
        // 1️⃣ Quotation status check
        if ($proforma->status !== 'accepted') {
            return back()->with('error', 'PI must be accepted first.');
            // abort(403, 'PI must be accepted first.');
        }

        if ($proforma->invoice) {
            return back()->with('error', 'Invoice already created.');
        }

        // 2️⃣ Validate mandatory PO fields
        $validated = $request->validate([
            'client_po_ref' => 'required|string|max:255',
            'client_po_pdf' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        // 3️⃣ Upload PO PDF
        $clientPoPdfPath = $request->file('client_po_pdf')
            ->store('client_po_pdfs', 'public');

        // 4️⃣ Create Invoice inside transaction
        $invoice = DB::transaction(function () use ($proforma, $validated, $clientPoPdfPath) {

            $invoice = SalesInvoice::create([
                'client_id' => $proforma->client_id,
                'quotation_id' => $proforma->id,
                'proforma_id' => $proforma->id,
                'invoice_number' => $this->generateInvoiceNumber()->getData()->invoice_number,
                'invoice_date' => now(),
                'status' => 'issued',

                // 🔑 FROM FORM
                'client_po_ref' => $validated['client_po_ref'],
                'client_po_pdf' => $clientPoPdfPath,

                'tax_type' => $proforma->tax_type,
                'subtotal' => $proforma->subtotal,
                'cgst_amount' => $proforma->cgst_amount,
                'sgst_amount' => $proforma->sgst_amount,
                'igst_amount' => $proforma->igst_amount,
                'grand_total' => $proforma->grand_total,
                'created_by' => auth()->id(),
            ]);

            // Copy items
            foreach ($proforma->items as $item) {
                $quantity = $item->quantity;

                // Effective price AFTER discount
                $effectiveUnitPrice = $quantity > 0
                    ? round($item->taxable_amount / $quantity, 2)
                    : 0;

                $invoice->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $quantity,

                    // Discount baked into price
                    'unit_price' => $effectiveUnitPrice,

                    // Clean totals
                    'taxable_amount' => $item->taxable_amount,
                    'tax_rate' => $item->tax_rate,
                    'tax_amount' => $item->tax_amount,
                    'total_with_tax' => $item->total_with_tax,
                ]);

            }

            // Update quotation status
            $proforma->update(['status' => 'converted']);

            return $invoice;
        });


        return redirect()
            ->route('invoices.show', $invoice->id)
            ->with('success', 'Invoice created successfully from PI.');
    }




}
