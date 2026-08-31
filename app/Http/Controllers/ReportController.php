<?php

namespace App\Http\Controllers;

use App\Models\SalesQuotation;
use App\Models\SalesProforma;
use App\Models\SalesInvoice;
use App\Models\PurchaseOrder;
use App\Models\Client;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Order Reports main page
     */
    public function orderReport(Request $request)
    {
        $clients = Client::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        // Date range defaults
        $fromDate = $request->filled('from_date')
            ? Carbon::parse($request->from_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $toDate = $request->filled('to_date')
            ? Carbon::parse($request->to_date)->endOfDay()
            : Carbon::now()->endOfDay();

        /* =================== SALES QUOTATIONS =================== */
        $quotationsQuery = SalesQuotation::with('client', 'creator')
            ->whereBetween('quotation_date', [$fromDate, $toDate]);

        if ($request->filled('client_id')) {
            $quotationsQuery->where('client_id', $request->client_id);
        }
        if ($request->filled('status')) {
            $quotationsQuery->where('status', $request->status);
        }

        $quotations = $quotationsQuery->latest('quotation_date')->get();

        /* =================== PROFORMA INVOICES =================== */
        $proformasQuery = SalesProforma::with('client', 'creator')
            ->whereBetween('proforma_date', [$fromDate, $toDate]);

        if ($request->filled('client_id')) {
            $proformasQuery->where('client_id', $request->client_id);
        }
        if ($request->filled('status')) {
            $proformasQuery->where('status', $request->status);
        }

        $proformas = $proformasQuery->latest('proforma_date')->get();

        /* =================== SALES INVOICES =================== */
        $invoicesQuery = SalesInvoice::with('client', 'creator')
            ->whereBetween('invoice_date', [$fromDate, $toDate]);

        if ($request->filled('client_id')) {
            $invoicesQuery->where('client_id', $request->client_id);
        }
        if ($request->filled('status')) {
            $invoicesQuery->where('status', $request->status);
        }

        $invoices = $invoicesQuery->latest('invoice_date')->get();

        /* =================== PURCHASE ORDERS =================== */
        $purchaseOrdersQuery = PurchaseOrder::with('supplier', 'createdBy')
            ->whereBetween('ordered_date', [$fromDate, $toDate]);

        if ($request->filled('supplier_id')) {
            $purchaseOrdersQuery->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('po_status')) {
            $purchaseOrdersQuery->where('status', $request->po_status);
        }

        $purchaseOrders = $purchaseOrdersQuery->latest('ordered_date')->get();

        /* =================== SUMMARY METRICS =================== */
        $summary = [
            'total_quotations' => $quotations->count(),
            'quotation_value' => $quotations->sum('grand_total'),
            'total_proformas' => $proformas->count(),
            'proforma_value' => $proformas->sum('grand_total'),
            'total_invoices' => $invoices->count(),
            'invoice_value' => $invoices->sum('grand_total'),
            'total_po' => $purchaseOrders->count(),
            'po_value' => $purchaseOrders->sum('grand_total'),
            'paid_invoices' => $invoices->where('status', 'paid')->count(),
            'unpaid_invoices' => $invoices->where('status', 'unpaid')->count(),
            'pending_quotations' => $quotations->where('status', 'pending')->count(),
        ];

        // Handle CSV export
        if ($request->has('export') && $request->export === 'csv') {
            return $this->exportCsv($request->export_type, $quotations, $proformas, $invoices, $purchaseOrders);
        }

        return view('order-report.order-report-index', compact(
            'quotations',
            'proformas',
            'invoices',
            'purchaseOrders',
            'clients',
            'suppliers',
            'summary',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Export to CSV
     */
    private function exportCsv($type, $quotations, $proformas, $invoices, $purchaseOrders)
    {
        $filename = $type . '_report_' . now()->format('Y-m-d') . '.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=$filename"];

        $callback = function () use ($type, $quotations, $proformas, $invoices, $purchaseOrders) {
            $file = fopen('php://output', 'w');

            match ($type) {
                'quotations' => $this->writeQuotationsCsv($file, $quotations),
                'proformas' => $this->writeProformasCsv($file, $proformas),
                'invoices' => $this->writeInvoicesCsv($file, $invoices),
                'purchase_orders' => $this->writePurchaseOrdersCsv($file, $purchaseOrders),
                default => $this->writeAllCsv($file, $quotations, $proformas, $invoices, $purchaseOrders),
            };

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function writeQuotationsCsv($file, $quotations)
    {
        fputcsv($file, ['Quotation #', 'Date', 'Client', 'Status', 'Grand Total']);
        foreach ($quotations as $q) {
            fputcsv($file, [$q->quotation_number, $q->quotation_date, $q->client->company_name ?? '-', $q->status, $q->grand_total]);
        }
    }

    private function writeProformasCsv($file, $proformas)
    {
        fputcsv($file, ['Proforma #', 'Date', 'Client', 'Status', 'Grand Total']);
        foreach ($proformas as $p) {
            fputcsv($file, [$p->proforma_number, $p->proforma_date, $p->client->company_name ?? '-', $p->status, $p->grand_total]);
        }
    }

    private function writeInvoicesCsv($file, $invoices)
    {
        fputcsv($file, ['Invoice #', 'Date', 'Client', 'Status', 'Grand Total', 'Payment Mode']);
        foreach ($invoices as $inv) {
            fputcsv($file, [$inv->invoice_number, $inv->invoice_date, $inv->client->company_name ?? '-', $inv->status, $inv->grand_total, $inv->payment_mode ?? '-']);
        }
    }

    private function writePurchaseOrdersCsv($file, $purchaseOrders)
    {
        fputcsv($file, ['PO #', 'Date', 'Supplier', 'Status', 'Grand Total']);
        foreach ($purchaseOrders as $po) {
            fputcsv($file, [$po->po_number, $po->ordered_date, $po->supplier->name ?? '-', $po->status, $po->grand_total]);
        }
    }

    private function writeAllCsv($file, $quotations, $proformas, $invoices, $purchaseOrders)
    {
        fputcsv($file, ['=== SALES QUOTATIONS ===']);
        $this->writeQuotationsCsv($file, $quotations);
        fputcsv($file, []);
        fputcsv($file, ['=== PROFORMA INVOICES ===']);
        $this->writeProformasCsv($file, $proformas);
        fputcsv($file, []);
        fputcsv($file, ['=== TAX INVOICES ===']);
        $this->writeInvoicesCsv($file, $invoices);
        fputcsv($file, []);
        fputcsv($file, ['=== PURCHASE ORDERS ===']);
        $this->writePurchaseOrdersCsv($file, $purchaseOrders);
    }
}
