@extends('layouts.app')

@section('title', 'Order Reports')

@push('styles')
<style>
.metric-card {
    border-radius: 12px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.metric-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important;
}
.metric-card .icon-wrap {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
}
.report-tab .nav-link {
    border-radius: 8px 8px 0 0;
    font-size: 13px;
    padding: 8px 18px;
}
.report-tab .nav-link.active { font-weight: 600; }

.badge-status {
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 20px;
}

.filter-bar { background: #f8f9fa; border-radius: 10px; padding: 14px 18px; margin-bottom: 18px; }

.table th { font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
.table td { font-size: 13px; vertical-align: middle; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="my-primary-color mb-0"><i class="fa-solid fa-book me-2"></i>Order Reports</h5>
            <small class="text-muted">
                <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
                <i class="fa-solid fa-angle-right"></i> Order Reports
            </small>
        </div>
        {{-- Export All Button --}}
        <div class="d-flex gap-2">
            <form method="GET" action="{{ route('reports.order') }}">
                @foreach(request()->except(['export','export_type']) as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach
                <input type="hidden" name="export" value="csv">
                <input type="hidden" name="export_type" value="all">
                <button class="btn btn-sm btn-outline-success">
                    <i class="fa fa-file-csv me-1"></i> Export All CSV
                </button>
            </form>
        </div>
    </div>

    {{-- Summary Metrics --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-md-3">
            <div class="card metric-card shadow-sm h-100 border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-wrap bg-primary bg-opacity-10 text-primary">
                        <i class="fa fa-file-invoice"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Tax Invoices</div>
                        <div class="fw-bold fs-5">{{ $summary['total_invoices'] }}</div>
                        <div class="small text-success">₹ {{ number_format($summary['invoice_value'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card metric-card shadow-sm h-100 border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-wrap bg-warning bg-opacity-10 text-warning">
                        <i class="fa fa-file-alt"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Quotations</div>
                        <div class="fw-bold fs-5">{{ $summary['total_quotations'] }}</div>
                        <div class="small text-success">₹ {{ number_format($summary['quotation_value'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card metric-card shadow-sm h-100 border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-wrap bg-info bg-opacity-10 text-info">
                        <i class="fa fa-file-contract"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Proformas</div>
                        <div class="fw-bold fs-5">{{ $summary['total_proformas'] }}</div>
                        <div class="small text-success">₹ {{ number_format($summary['proforma_value'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card metric-card shadow-sm h-100 border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-wrap bg-success bg-opacity-10 text-success">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Purchase Orders</div>
                        <div class="fw-bold fs-5">{{ $summary['total_po'] }}</div>
                        <div class="small text-success">₹ {{ number_format($summary['po_value'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('reports.order') }}" class="row g-2 align-items-end">

            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">From Date</label>
                <input type="date" name="from_date" class="form-control form-control-sm"
                    value="{{ request('from_date', $fromDate->format('Y-m-d')) }}">
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">To Date</label>
                <input type="date" name="to_date" class="form-control form-control-sm"
                    value="{{ request('to_date', $toDate->format('Y-m-d')) }}">
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Client</label>
                <select name="client_id" class="form-select form-select-sm">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->company_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Supplier</label>
                <select name="supplier_id" class="form-select form-select-sm">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Sales Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">PO Status</label>
                <select name="po_status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="draft" {{ request('po_status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="approved" {{ request('po_status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="received" {{ request('po_status') === 'received' ? 'selected' : '' }}>Received</option>
                    <option value="cancelled" {{ request('po_status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fa fa-filter me-1"></i> Apply Filters
                </button>
                <a href="{{ route('reports.order') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-times me-1"></i> Clear
                </a>
            </div>

        </form>
    </div>

    {{-- Tabs for different report types --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <ul class="nav nav-tabs report-tab px-3 pt-3" id="reportTabs">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#invoicesTab">
                        <i class="fa fa-file-invoice me-1"></i> Tax Invoices
                        <span class="badge bg-primary ms-1">{{ $invoices->count() }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#quotationsTab">
                        <i class="fa fa-file-alt me-1"></i> Quotations
                        <span class="badge bg-warning text-dark ms-1">{{ $quotations->count() }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#proformasTab">
                        <i class="fa fa-file-contract me-1"></i> Proformas
                        <span class="badge bg-info text-dark ms-1">{{ $proformas->count() }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#purchaseTab">
                        <i class="fa fa-shopping-cart me-1"></i> Purchase Orders
                        <span class="badge bg-success ms-1">{{ $purchaseOrders->count() }}</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content p-3">

                {{-- ===== TAX INVOICES ===== --}}
                <div class="tab-pane fade show active" id="invoicesTab">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold">Tax Invoices</h6>
                        <form method="GET" action="{{ route('reports.order') }}" class="d-inline">
                            @foreach(request()->except(['export','export_type']) as $key => $val)
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endforeach
                            <input type="hidden" name="export" value="csv">
                            <input type="hidden" name="export_type" value="invoices">
                            <button class="btn btn-xs btn-outline-success btn-sm">
                                <i class="fa fa-download me-1"></i> Export CSV
                            </button>
                        </form>
                    </div>
                    @if($invoices->isEmpty())
                        <div class="alert alert-light text-center text-muted py-4">
                            <i class="fa fa-inbox fa-2x mb-2 d-block"></i> No invoices found for the selected period.
                        </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Invoice No.</th>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th>Payment Mode</th>
                                    <th class="text-end">Grand Total</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $i => $invoice)
                                <tr>
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</td>
                                    <td>{{ $invoice->client->company_name ?? '-' }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($invoice->status) {
                                                'paid' => 'bg-success',
                                                'unpaid' => 'bg-warning text-dark',
                                                'partially_paid' => 'bg-info text-dark',
                                                'cancelled' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge badge-status {{ $badgeClass }}">{{ ucfirst(str_replace('_',' ',$invoice->status)) }}</span>
                                    </td>
                                    <td>{{ $invoice->payment_mode ?? '-' }}</td>
                                    <td class="text-end fw-semibold">₹ {{ number_format($invoice->grand_total, 2) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-xs btn-sm btn-outline-primary" title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('sales-orders.invoices.print', $invoice->id) }}" class="btn btn-xs btn-sm btn-outline-secondary" title="Print" target="_blank">
                                            <i class="fa fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="6" class="text-end">Total</td>
                                    <td class="text-end">₹ {{ number_format($invoices->sum('grand_total'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif
                </div>

                {{-- ===== QUOTATIONS ===== --}}
                <div class="tab-pane fade" id="quotationsTab">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold">Sales Quotations</h6>
                        <form method="GET" action="{{ route('reports.order') }}" class="d-inline">
                            @foreach(request()->except(['export','export_type']) as $key => $val)
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endforeach
                            <input type="hidden" name="export" value="csv">
                            <input type="hidden" name="export_type" value="quotations">
                            <button class="btn btn-xs btn-outline-success btn-sm">
                                <i class="fa fa-download me-1"></i> Export CSV
                            </button>
                        </form>
                    </div>
                    @if($quotations->isEmpty())
                        <div class="alert alert-light text-center text-muted py-4">
                            <i class="fa fa-inbox fa-2x mb-2 d-block"></i> No quotations found for the selected period.
                        </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Quotation No.</th>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th>Converted To</th>
                                    <th class="text-end">Grand Total</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quotations as $i => $q)
                                <tr>
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td><strong>{{ $q->quotation_number }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($q->quotation_date)->format('d M Y') }}</td>
                                    <td>{{ $q->client->company_name ?? '-' }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($q->status) {
                                                'accepted' => 'bg-success',
                                                'pending' => 'bg-warning text-dark',
                                                'rejected' => 'bg-danger',
                                                'draft' => 'bg-secondary',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge badge-status {{ $badgeClass }}">{{ ucfirst($q->status) }}</span>
                                    </td>
                                    <td>
                                        @if($q->proforma_id)
                                            <span class="badge bg-info text-dark">Proforma</span>
                                        @elseif($q->invoice_id)
                                            <span class="badge bg-primary">Invoice</span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold">₹ {{ number_format($q->grand_total, 2) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('quotations.show', $q->id) }}" class="btn btn-xs btn-sm btn-outline-primary" title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('sales-orders.quotations.print', $q->id) }}" class="btn btn-xs btn-sm btn-outline-secondary" title="Print" target="_blank">
                                            <i class="fa fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="6" class="text-end">Total</td>
                                    <td class="text-end">₹ {{ number_format($quotations->sum('grand_total'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif
                </div>

                {{-- ===== PROFORMA INVOICES ===== --}}
                <div class="tab-pane fade" id="proformasTab">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold">Proforma Invoices</h6>
                        <form method="GET" action="{{ route('reports.order') }}" class="d-inline">
                            @foreach(request()->except(['export','export_type']) as $key => $val)
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endforeach
                            <input type="hidden" name="export" value="csv">
                            <input type="hidden" name="export_type" value="proformas">
                            <button class="btn btn-xs btn-outline-success btn-sm">
                                <i class="fa fa-download me-1"></i> Export CSV
                            </button>
                        </form>
                    </div>
                    @if($proformas->isEmpty())
                        <div class="alert alert-light text-center text-muted py-4">
                            <i class="fa fa-inbox fa-2x mb-2 d-block"></i> No proformas found for the selected period.
                        </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Proforma No.</th>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th class="text-end">Grand Total</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proformas as $i => $p)
                                <tr>
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td><strong>{{ $p->proforma_number }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($p->proforma_date)->format('d M Y') }}</td>
                                    <td>{{ $p->client->company_name ?? '-' }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($p->status) {
                                                'accepted' => 'bg-success',
                                                'pending' => 'bg-warning text-dark',
                                                'draft' => 'bg-secondary',
                                                'cancelled' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge badge-status {{ $badgeClass }}">{{ ucfirst($p->status) }}</span>
                                    </td>
                                    <td class="text-end fw-semibold">₹ {{ number_format($p->grand_total, 2) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('proformas.show', $p->id) }}" class="btn btn-xs btn-sm btn-outline-primary" title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('sales-orders.proformas.print', $p->id) }}" class="btn btn-xs btn-sm btn-outline-secondary" title="Print" target="_blank">
                                            <i class="fa fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="5" class="text-end">Total</td>
                                    <td class="text-end">₹ {{ number_format($proformas->sum('grand_total'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif
                </div>

                {{-- ===== PURCHASE ORDERS ===== --}}
                <div class="tab-pane fade" id="purchaseTab">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold">Purchase Orders</h6>
                        <form method="GET" action="{{ route('reports.order') }}" class="d-inline">
                            @foreach(request()->except(['export','export_type']) as $key => $val)
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endforeach
                            <input type="hidden" name="export" value="csv">
                            <input type="hidden" name="export_type" value="purchase_orders">
                            <button class="btn btn-xs btn-outline-success btn-sm">
                                <i class="fa fa-download me-1"></i> Export CSV
                            </button>
                        </form>
                    </div>
                    @if($purchaseOrders->isEmpty())
                        <div class="alert alert-light text-center text-muted py-4">
                            <i class="fa fa-inbox fa-2x mb-2 d-block"></i> No purchase orders found for the selected period.
                        </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>PO No.</th>
                                    <th>Date</th>
                                    <th>Supplier</th>
                                    <th>Status</th>
                                    <th class="text-end">Grand Total</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrders as $i => $po)
                                <tr>
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td><strong>{{ $po->po_number }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($po->ordered_date)->format('d M Y') }}</td>
                                    <td>{{ $po->supplier->name ?? '-' }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($po->status) {
                                                'approved' => 'bg-success',
                                                'received' => 'bg-primary',
                                                'draft' => 'bg-secondary',
                                                'cancelled' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge badge-status {{ $badgeClass }}">{{ ucfirst($po->status) }}</span>
                                    </td>
                                    <td class="text-end fw-semibold">₹ {{ number_format($po->grand_total, 2) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('purchase-orders.show', $po->id) }}" class="btn btn-xs btn-sm btn-outline-primary" title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('purchase-orders.pdf', $po->id) }}" class="btn btn-xs btn-sm btn-outline-secondary" title="Print" target="_blank">
                                            <i class="fa fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="5" class="text-end">Total</td>
                                    <td class="text-end">₹ {{ number_format($purchaseOrders->sum('grand_total'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
