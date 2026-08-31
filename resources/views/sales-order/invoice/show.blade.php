@extends('layouts.app')

@section('title')
    Tax Invoice Details
@endsection

@section('content')
    <div class="container mt-2">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold my-primary-color">
                <i class="fas fa-file-invoice me-2"></i> {{ $order->invoice_number }}
            </h5>

            <!-- <div>
                @if ($order->status == 'draft')
                    <button class="btn btn-sm btn-primary update-invoice-btn" data-bs-toggle="modal" data-bs-target="#updateInvoiceModal" data-invoice='@json($order)' data-update-url="{{ route('invoices.update', $order->id) }}">
                       <i class="fas fa-edit"></i> Edit
                    </button>
                @endif
                <a href="{{ route('invoices.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div> -->
            <div class="d-flex gap-2">

                {{-- View Source Quotation --}}
                @if ($order->quotation)
                    <a href="{{ route('quotations.show', $order->quotation->id) }}"
                    class="btn btn-sm btn-outline-info"
                    target="_blank">
                        <i class="fas fa-file-alt"></i> View Quotation
                    </a>
                @endif

                {{-- View Source Proforma --}}
                @if ($order->proforma)
                    <a href="{{ route('proformas.show', $order->proforma->id) }}"
                    class="btn btn-sm btn-outline-warning"
                    target="_blank">
                        <i class="fas fa-file-invoice"></i> View Proforma
                    </a>
                @endif

                {{-- Edit Invoice --}}
                @if ($order->status == 'draft')
                    <button class="btn btn-sm btn-primary update-invoice-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#updateInvoiceModal"
                            data-invoice='@json($order)'>
                        <i class="fas fa-edit"></i> Edit
                    </button>
                @endif

                {{-- Back --}}
                <a href="{{ route('invoices.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>

            </div>
        </div>

        <div class="mb-3">
            <small class="text-muted">
                <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
                <i class="fa-solid fa-angle-right"></i>
                <a class="text-decoration-none" href="{{ route('invoices.index') }}">Tax Invoice</a>
                <i class="fa-solid fa-angle-right"></i> {{ $order->invoice_number }}
            </small>
        </div>

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-1" id="salesOrderTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab-details">
                    <i class="fas fa-info-circle"></i> Details
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-items">
                    <i class="fas fa-list"></i> Items
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-client">
                    <i class="fas fa-user"></i> Client
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-communication">
                    <i class="fas fa-message"></i> Communication
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-payments">
                    <i class="fas fa-credit-card"></i> Payments
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-logs">
                    <i class="fas fa-history"></i> Activity Log
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-qr">
                    <i class="fas fa-qrcode"></i> QR Code
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-remarks">
                    <i class="fas fa-comment-alt"></i> Remarks
                </a>
            </li>
        </ul>

        <div class="tab-content">

            {{-- DETAILS TAB --}}
            <div class="tab-pane fade show active" id="tab-details">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="fw-bold text-secondary">Order Information</h5>
                        <hr>
                        <p><strong>Sales Order Number:</strong> {{ $order->invoice_number }}</p>
                        <p><strong>Order Date:</strong> {{ $order->invoice_date }}</p>
                        <p><strong>Client PO Ref:</strong> {{ $order->client_po_ref }}</p>

                        @if($order->quotation)
                            <p>
                                <strong>Quotation Ref:</strong>
                                <a href="{{ route('quotations.show', $order->quotation->id) }}">
                                    <span class="badge bg-info">{{ $order->quotation->quotation_number }}</span>
                                </a>
                            </p>
                        @endif

                        @if($order->proforma)
                            <p>
                                <strong>PI Ref:</strong>
                                <a href="{{ route('proformas.show', $order->proforma->id) }}">
                                    <span class="badge bg-warning">{{ $order->proforma->proforma_number }}</span>
                                </a>
                            </p>
                        @endif
                        </p>
                        <p><strong>Status:</strong>
                            <span class="badge 
                                @if($order->status == 'draft') bg-secondary
                                @elseif($order->status == 'paid') bg-primary
                                @elseif($order->status == 'partially_paid') bg-info
                                @elseif($order->status == 'issued') bg-info
                                @elseif($order->status == 'rejected') bg-danger
                                @elseif($order->status == 'delivered') bg-success
                                @endif
                            ">
                                {{ ucfirst($order->status) }}
                            </span>
                        </p>
                        <p><strong>Payment Mode:</strong> {{ $order->payment_mode ?? 'N/A' }}</p>
                        <p><strong>Created By:</strong> {{ $order->creator->name ?? 'N/A' }}</p>
                        <p><strong>Last Updated on:</strong> {{ $order->updated_at }}</p>
                        <p><strong>Total Amount:</strong> ₹{{ inr_format($order->grand_total) }}</p>
                        @if ($order->client_po_pdf)
                            <a href="{{ asset('storage/'.$order->client_po_pdf) }}" 
                            class="btn btn-sm btn-outline-info" 
                            target="_blank">
                            Download Client's PO
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ITEMS TAB --}}
            <div class="tab-pane fade" id="tab-items">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="fw-bold text-secondary">Ordered Items</h5>
                        <hr>
                        @if($order->items->count())

                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" data-invoice='@json($order)' class="btn btn-sm btn-primary edit-items-btn" id="openEditItemsModal" data-bs-toggle="modal" data-bs-target="#editItemsModal">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit Items
                                </button>
                            </div>

                            <table class="table table-bordered" id="itemsTable" data-order-id="{{ $order->id }}">

                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Specs</th>
                                        <th>Qty</th>
                                        <th>Rate</th>
                                        <th>Tax Rate (%)</th>
                                        <th>Taxable</th>
                                        <th>Tax</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="small-text">
                                    @foreach($order->items as $item)
                                        <tr class="item-row" data-id="{{ $item->id }}" data-product="{{ $item->product_id }}">
                                            <td class="text-center">{{ $loop->iteration}}</td>
                                            <td><strong>{{ $item->product->our_part_no }}</strong> <br><small>{{ $item->product->description }}</small></td>
                                            <td>{{ $item->product->specs ?? '-'}}</td>
                                            <td class="text-center">{{ $item->quantity }}</td->
                                            <td>{{ $item->unit_price }}</td>
                                            <td class="text-center">{{ $item->tax_rate }}</td>
                                            <td>{{ $item->taxable_amount }}</td>
                                            <td>{{ $item->tax_amount }}</td>
                                            <td>{{ $item->total_with_tax }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('products.show', $item->product->id) }}"
                                                    class="btn btn-sm btn-outline-primary"> <i class="fas fa-eye"></i> </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="9" class="text-end">Subtotal</th>
                                        <th>₹ {{ inr_format($order->subtotal) }}</th>
                                    </tr>
                                    
                                    @php
                                        $cgstRatio = (float) \App\Models\Setting::get('cgst_division_percentage', 50);
                                        $sgstRatio = (float) \App\Models\Setting::get('sgst_division_percentage', 50);
                                        $totalRatio = $cgstRatio + $sgstRatio ?: 1;
                                        
                                        $taxRates = $order->items->pluck('tax_rate')->unique();
                                        $commonTaxRate = count($taxRates) === 1 ? $taxRates->first() : null;
                                    @endphp

                                    @if ($order->tax_type === 'igst')
                                        <tr>
                                            <th colspan="9" class="text-end">IGST @if($commonTaxRate) ({{ number_format($commonTaxRate, 2) + 0 }}%) @endif</th>
                                            <td>₹ {{ inr_format($order->igst_amount) }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <th colspan="9" class="text-end">CGST @if($commonTaxRate) ({{ number_format($commonTaxRate * $cgstRatio / $totalRatio, 2) + 0 }}%) @endif</th>
                                            <td>₹ {{ inr_format($order->cgst_amount) }}</td>
                                        </tr>
                                        <tr>
                                            <th colspan="9" class="text-end">SGST @if($commonTaxRate) ({{ number_format($commonTaxRate * $sgstRatio / $totalRatio, 2) + 0 }}%) @endif</th>
                                            <td>₹ {{ inr_format($order->sgst_amount) }}</td>
                                        </tr>
                                    @endif
                                    
                                    <tr>
                                        <th colspan="9" class="text-end">Grand Total</th>
                                        <th>₹ {{ inr_format($order->grand_total) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        @else
                            <p class="text-muted">No items added to this order.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CLIENT TAB --}}
            <div class="tab-pane fade" id="tab-client">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="fw-bold text-secondary">Client Information</h5>
                        <hr>
                        <p><strong>Name:</strong> {{ $order->client->name }}</p>
                        <p><strong>Email:</strong> {{ $order->client->email }}</p>
                        <p><strong>Phone:</strong> {{ $order->client->phone }}</p>
                        <p><strong>Shipping Address:</strong> {{ $order->shippingAddress->full_address ?? $order->client->shipping_address }}</p>
                        <p><strong>Billing Address:</strong> {{ $order->billingAddress->full_address ?? $order->client->billing_address }}</p>
                    </div>
                </div>
            </div>

            {{-- PAYMENTS TAB --}}
            <div class="tab-pane fade" id="tab-payments">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="fw-bold text-secondary">Payment History</h5>

                            @if ($order->status != 'paid')
                                <button class="btn btn-sm btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#makePaymentModal">
                                    <i class="fas fa-plus"></i> Add Payment
                                </button>
                            @endif
                            
                        </div>

                        <hr>

                        @if ($order->payments->count())
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Mode</th>
                                        <th>Reference No</th>
                                        <th>Transaction Id</th>
                                        <th>Received By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->payments as $payment)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ \Carbon\Carbon::parse($payment->paid_at)->format('d-m-Y H:i') }}</td>
                                            <td>₹ {{ inr_format($payment->amount) }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_mode)) }}</td>
                                            <td>{{ $payment->reference_no }}</td>
                                            <td>{{ $payment->transaction_id }}</td>
                                            <td>{{ $payment->creator->name ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td colspan="2" class="text-end">Total Paid</td>
                                        <td colspan="4">
                                            ₹ {{ inr_format($order->payments->sum('amount')) }}
                                        </td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <td colspan="2" class="text-end">Balance</td>
                                        <td colspan="4">
                                            ₹ {{ inr_format($order->grand_total - $order->payments->sum('amount')) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        @else
                            <p class="text-muted">No payments recorded for this invoice.</p>
                        @endif

                    </div>
                </div>
            </div>


            {{-- COMMUNICATION TAB --}}
            <div class="tab-pane fade" id="tab-communication">
                <div class="card shadow-sm mb-3">

                    {{-- Overlay when not paid --}}
                    @unless($order->status === 'paid')
                        <div class="position-absolute w-100 h-100 bg-white"
                            style="opacity:0.5; z-index:10;"></div>
                    @endunless

                    <div class="card-body {{ !$order->status === 'paid' ? 'opacity-75 pointer-events-none' : '' }}">

                        {{-- Warning Message --}}
                        @unless($order->status === 'paid')
                            <div class="alert alert-warning text-center mb-3">
                                <i class="fas fa-lock me-1"></i>
                                Invoice must be <strong>fully paid</strong> before sending email to customer.
                            </div>
                        @endunless

                        {{-- SEND EMAIL --}}
                        <form action="{{ route('sales-orders.invoices.send-mail', $order->id) }}" method="POST">
                            @csrf

                            <div class="row g-2">

                                <div class="col-md-4">
                                    <label class="form-label">From</label>
                                    <select name="from_email" class="form-select form-select-sm" {{ !$order->status === 'paid' ? 'disabled' : '' }}>
                                        <option value="info@tetechsolution.com">info@tetechsolution.com</option>
                                        <option value="sales@tetechsolution.com">sales@tetechsolution.com</option>
                                    </select>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label">To</label>
                                    <input type="text"
                                        name="to"
                                        class="form-control form-control-sm"
                                        value="{{ $order->client->email }}"
                                        {{ !$order->status === 'paid' ? 'disabled' : '' }}>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">CC</label>
                                    <input type="text"
                                        name="cc"
                                        class="form-control form-control-sm"
                                        {{ !$order->status === 'paid' ? 'disabled' : '' }}>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Subject</label>
                                    <input type="text"
                                        name="subject"
                                        class="form-control form-control-sm"
                                        value="Tax Invoice {{ $order->invoice_number }}"
                                        {{ !$order->status === 'paid' ? 'disabled' : '' }}>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Message (Optional)</label>
                                    <textarea name="body"
                                            class="form-control form-control-sm small-text"
                                            rows="4"
                                            {{ !$order->status === 'paid' ? 'disabled' : '' }}>.</textarea>
                                </div>

                                <div class="col-md-12 text-end">
                                    <button class="btn btn-primary" {{ !$order->status === 'paid' ? 'disabled' : '' }}>
                                        <i class="fas fa-paper-plane"></i> Send Email
                                    </button>
                                </div>

                            </div>
                        </form>

                        <hr>

                        {{-- EMAIL HISTORY --}}
                        <h6 class="fw-bold text-secondary">Email History</h6>

                        @forelse($order->communications as $mail)
                            <div class="border rounded p-2 mb-2">
                                <strong>{{ $mail->subject }}</strong><br>
                                <small class="text-muted">
                                    From: {{ $mail->from_email }} |
                                    To: {{ implode(', ', $mail->to_emails ?? []) }} |
                                    {{ $mail->sent_at }}
                                </small>
                            </div>
                        @empty
                            <p class="text-muted">No emails sent yet.</p>
                        @endforelse

                    </div>
                </div>
            </div>


            {{-- ACTIVITY LOG TAB --}}
            <div class="tab-pane fade" id="tab-logs">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="fw-bold text-secondary">Activity Log</h5>
                        <hr>
                        @if($order->activityLogs->count())
                            <ul class="list-group">
                                @foreach($order->activityLogs as $log)
                                    <li class="list-group-item">
                                        <strong>{{ $log->description }} by {{ $log->causer->name }}</strong><br>
                                        <small class="text-muted">{{ $log->created_at }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">No activity recorded.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- QR CODE TAB --}}
            <div class="tab-pane fade" id="tab-qr">
                <div class="card shadow-sm mb-3 text-center">
                    <div class="card-body">
                        <h5 class="fw-bold text-secondary">Order QR Code</h5>
                        <hr>
                        <img src="{{ $qrSvg }}" class="img-fluid" style="max-width:200px;">
                        <p class="text-muted mt-2">QR encodes: <strong>{{ $order->so_number }}</strong></p>
                        <p class="mt-3"><strong>Scan to view order</strong></p>
                    </div>
                </div>
            </div>

            {{-- REMARKS TAB --}}
            <div class="tab-pane fade" id="tab-remarks">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="fw-bold text-secondary">Remarks</h5>
                        <hr>
                        @if($order->remarks)
                            <div class="border rounded p-2 bg-light">
                                <pre class="m-0">{{ $order->remarks }}</pre>
                            </div>
                        @else
                            <p class="text-muted">No remarks added.</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>




        <!-- Edit Items Modal -->
        <div class="modal fade" id="editItemsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <form id="editItemsForm" method="POST" action="{{ route('sales-orders.invoices.items.update', $order) }}">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title my-primary-color">Edit Invoice Items</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <table class="table table-bordered" id="editItemsTable">
                                <thead class="table-light">
                                    <tr class="align-middle">
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Rate</th>
                                        <th>Tax %</th>
                                        <th>Taxable</th>
                                        <th>Tax</th>
                                        <th>Total</th>
                                        <th width="50">
                                            <button type="button"
                                                    class="btn btn-success btn-sm"
                                                    id="addEditItemRow">
                                                +
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                            <div class="row mt-3">
                                <div class="col-md-4 ms-auto">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Subtotal</th>
                                            <td>
                                                <input type="text" id="edit_subtotal"
                                                    class="form-control" readonly>
                                            </td>
                                        </tr>
                                        <tr class="tax-row">
                                            <th>Total Tax</th>
                                            <td>
                                                <input type="text" id="edit_total_tax"
                                                    class="form-control" readonly>
                                            </td>
                                        </tr>
                                        <tr class="cgst-sgst-row">
                                            <th id="cgst_label_edit">CGST</th>
                                            <td><input type="text" id="edit_cgst_amount" class="form-control" readonly></td>
                                        </tr>
                                        <tr class="cgst-sgst-row">
                                            <th id="sgst_label_edit">SGST</th>
                                            <td><input type="text" id="edit_sgst_amount" class="form-control" readonly></td>
                                        </tr>
                                        <tr class="igst-row" style="display:none;">
                                            <th id="igst_label_edit">IGST</th>
                                            <td><input type="text" id="edit_igst_amount" class="form-control" readonly></td>
                                        </tr>
                                        <input type="hidden" id="edit_tax_type" value="{{ $order->tax_type }}">
                                        <tr>
                                            <th>Grand Total</th>
                                            <td>
                                                <input type="text" id="edit_grand_total"
                                                    class="form-control" readonly>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">
                                Cancel
                            </button>

                            <button type="submit"
                                    class="btn btn-success">
                                Update Items
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>





        {{-- Status & PDF Actions --}}
        <div class="d-flex gap-2 mt-3 mb-4">
            <a href="{{ route('sales-orders.invoices.print', $order->id) }}" class="btn btn-dark">
                <i class="fa fa-file-pdf"></i> Download PDF
            </a>

             @if ($order->status != 'paid')
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#makePaymentModal">
                    Make Payment
                </button>
            @else
                <button class="btn btn-secondary" disabled>
                    Fully Paid
                </button>
            @endif
        </div>

    </div>

    <!-- Update Invoice Modal -->
    <div class="modal fade" id="updateInvoiceModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <form id="updateInvoiceForm" method="POST" action="{{ route('invoices.update', $order) }}">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title my-primary-color">Update Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="update_invoice_id">

                    <div class="row g-3">

                        <div class="col-md-3">
                            <label class="form-label required-field">Invoice Date</label>
                            <input type="date" class="form-control" name="invoice_date" value="{{ $order->invoice_date }}" id="updateInvoiceDate">
                            <!-- <input type="hidden" name="invoice_date" id="updateInvoiceDateHidden"> -->
                        </div>

                        <div class="col-md-3">
                            <label class="form-label required-field">Invoice No</label>
                            <input type="text" class="form-control" id="updateInvoiceNumber" value="{{ $order->invoice_number }}" disabled>
                            <input type="hidden" name="invoice_number" value="{{ $order->invoice_number }}" id="updateInvoiceNumberHidden">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Payment Mode</label>
                            <select name="payment_mode" id="updatePaymentMode" class="form-select">
                                <option value="{{ $order->payment_mode }}" selected>{{ucfirst($order->payment_mode) }}</option>
                                <option value="Net Banking">NetBanking</option>
                                <option value="Cash">Cash</option>
                                <option value="UPI">UPI</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label required-field">Client</label>
                            <select name="client_id" id="updateClient" class="form-select">
                                <option value="">Select Client</option>
                                @foreach ($clients as $c)
                                    <option value="{{ $c->id }}" 
                                        {{ $c->id == $order->client_id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <a type="button" class="my-primary-color" style="font-size: 12px;"
                                    data-bs-toggle="offcanvas" data-bs-target="#clientOffcanvas">
                                    <i class="fa-solid fa-plus"></i> New
                                </a>
                                <a type="button" class="my-primary-color edit-client-shortcut" style="font-size: 12px;"
                                    data-bs-toggle="offcanvas" data-bs-target="#editClientOffcanvas">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label required-field">Billing Address</label>
                            <select name="billing_address_id" id="update_billing_address_id" class="form-select" required>
                                <option value="">Select Billing Address</option>
                            </select>
                            <div class="d-flex align-items-center gap-2 mt-1 address-actions">
                                <a type="button" class="my-primary-color" style="font-size: 12px;"
                                    data-bs-toggle="offcanvas" data-bs-target="#addressOffcanvas" data-target-select="update_billing_address_id">
                                    <i class="fa-solid fa-plus"></i> New
                                </a>
                                <a type="button" class="my-primary-color edit-address-shortcut" style="font-size: 12px;"
                                    data-bs-toggle="offcanvas" data-bs-target="#editAddressOffcanvas" data-select-id="update_billing_address_id">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label required-field">Shipping Address</label>
                            <select name="shipping_address_id" id="update_shipping_address_id" class="form-select" required>
                                <option value="">Select Shipping Address</option>
                            </select>
                            <div class="d-flex align-items-center gap-2 mt-1 address-actions">
                                <a type="button" class="my-primary-color" style="font-size: 12px;"
                                    data-bs-toggle="offcanvas" data-bs-target="#addressOffcanvas" data-target-select="update_shipping_address_id">
                                    <i class="fa-solid fa-plus"></i> New
                                </a>
                                <a type="button" class="my-primary-color edit-address-shortcut" style="font-size: 12px;"
                                    data-bs-toggle="offcanvas" data-bs-target="#editAddressOffcanvas" data-select-id="update_shipping_address_id">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label required-field">Client's PO Ref No.</label>
                            <input type="text" value="{{ $order->client_po_ref }}" name="client_po_ref" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Client's PO (Optional)</label>
                            <input type="file" value="{{ $order->client_po_pdf }}" name="client_po_pdf" class="form-control" accept="application/pdf">
                        </div>

                        <div class="col-md-12 mt-3">
                            <table class="table table-bordered" id="updateItemsTable">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Rate</th>
                                        <th>Tax %</th>
                                        <th>Taxable</th>
                                        <th>Tax</th>
                                        <th>Total</th>
                                        <th width="50">
                                            <button type="button"
                                                class="btn btn-success btn-sm"
                                                id="addUpdateItemRow">+</button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div class="col-md-8">
                            {{-- Notes --}}
                            <div class="">
                                <label class="form-label">Notes/Instructions</label>
                                <textarea name="notes" class="form-control" rows="3" id="notes-field"
                                    style="font-size:0.9rem">{{ $order->notes }}</textarea>
                            </div>

                            {{-- Terms and Conditions --}}
                            <div class="">
                                <label class="form-label"> Terms and Conditions </label>
                                <textarea name="tnc" class="form-control" rows="3" id="tnc-field"
                                    style="font-size:0.9rem">{{ $order->tnc }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-4 ms-auto">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Subtotal</th>
                                    <td><input id="update_subtotal" value="{{ $order->subtotal }}" class="form-control" readonly></td>
                                </tr>
                                <tr>
                                     <th>Tax Type</th>
                                     <td>
                                         <select name="tax_type" id="tax_type" class="form-select">
                                             <option value="cgst_sgst"
                                                 {{ old('tax_type', $order->tax_type) == 'cgst_sgst' ? 'selected' : '' }}>
                                                 CGST + SGST
                                             </option>
                                             <option value="igst"
                                                 {{ old('tax_type', $order->tax_type) == 'igst' ? 'selected' : '' }}>
                                                 IGST
                                             </option>
                                         </select>
                                     </td>
                                </tr>
                                <tr>
                                    <th>Total Tax</th>
                                    <td><input id="update_total_tax" value="{{ $order->total_tax }}" class="form-control" readonly></td>
                                </tr>
                                <tr class="cgst-sgst-row">
                                    <th id="cgst_label">CGST</th>
                                    <td><input type="text" id="update_cgst_amount" class="form-control" readonly></td>
                                </tr>
                                <tr class="cgst-sgst-row">
                                    <th id="sgst_label">SGST</th>
                                    <td><input type="text" id="update_sgst_amount" class="form-control" readonly></td>
                                </tr>
                                <tr class="igst-row" style="display:none;">
                                    <th id="igst_label">IGST</th>
                                    <td><input type="text" id="update_igst_amount" class="form-control" readonly></td>
                                </tr>
                                <tr>
                                    <th>Grand Total</th>
                                    <td><input id="update_grand_total" name="grand_total" value="{{ $order->grand_total }}" class="form-control" readonly></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-12">
                            <label for="addSalesOrderRemarks" class="form-label">Remarks </label>
                            <textarea class="form-control" id="addSalesOrderRemarks" name="remarks" cols="3">{{ $order->remarks }}</textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Update Invoice</button>
                </div>

            </form>

        </div>
    </div>
    </div>

    <!-- Make Payment Modal -->
    <div class="modal fade" id="makePaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('invoices.payments.store', $order->id) }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Make Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label required-field">Amount</label>
                            <input type="number"
                                    name="amount"
                                    class="form-control"
                                    step="0.01"
                                    min="1"
                                    max="{{ ($order->grand_total - $order->payments->sum('amount')) }}"
                                    value="{{ ($order->grand_total - $order->payments->sum('amount')) }}"
                                    required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required-field">Payment Mode</label>
                            <select name="payment_mode" class="form-select" required>
                                <option value="">Select</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="upi">UPI</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required-field">Reference No</label>
                            <input type="text"
                                name="reference_no"
                                class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Transaction Id</label>
                            <input type="text"
                                name="transaction_id"
                                class="form-control"
                                >
                        </div>

                        <div class="mb-3">
                            <label class="form-label required-field">Paid At</label>
                            <input type="datetime-local"
                                name="paid_at"
                                class="form-control"
                                value="{{ now()->format('Y-m-d\TH:i') }}"
                                required>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">Save Payment</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection

@push('modals')
    {{-- Edit Client Offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="editClientOffcanvas" style="z-index: 1060;">
        <div class="offcanvas-header py-2">
            <h5 class="offcanvas-title my-primary-color">Edit Client</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form id="editClientForm">
                @csrf
                <input type="hidden" name="id" id="edit_client_id">
                <div class="row g-2">
                    <div class="col-12">
                        <label class="form-label required-field">Client Name</label>
                        <input type="text" class="form-control form-control-sm" name="name" id="edit_client_name" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label required-field">Contact Person</label>
                        <input type="text" class="form-control form-control-sm" name="contact_person" id="edit_client_contact_person" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label required-field">Email</label>
                        <input type="email" class="form-control form-control-sm" name="email" id="edit_client_email" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control form-control-sm" name="phone" id="edit_client_phone" maxlength="10">
                    </div>
                    <div class="col-12">
                        <label class="form-label">GST Number</label>
                        <input type="text" class="form-control form-control-sm" name="gst_number" id="edit_client_gst_number">
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-sm bg-my-primary text-white">Update Client</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Add Client Form --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="clientOffcanvas" style="z-index: 1060;">
        <div class="offcanvas-header py-2">
            <h5 class="offcanvas-title my-primary-color">Add New Client</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form id="addClientForm" data-url="{{ route('clients.ajax-store') }}">
                @csrf
                <div class="row g-2">
                    <div class="col-12">
                        <label class="form-label required-field">Client Name</label>
                        <input type="text" class="form-control form-control-sm" name="name" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label required-field">Contact Person</label>
                        <input type="text" class="form-control form-control-sm" name="contact_person" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label required-field">Email</label>
                        <input type="email" class="form-control form-control-sm" name="email" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control form-control-sm" name="phone" maxlength="10">
                    </div>
                    <div class="col-12">
                        <label class="form-label">GST Number</label>
                        <input type="text" class="form-control form-control-sm" name="gst_number">
                    </div>
                    <div class="col-12">
                        <label class="form-label required-field">Billing Address</label>
                        <a type="button" class="my-primary-color ms-1" style="font-size: 11px;" data-bs-toggle="offcanvas" data-bs-target="#addressOffcanvas" data-target-select="add_client_billing_address">
                            <i class="fa-solid fa-plus"></i> New Address
                        </a>
                        <select name="billing_address_id" id="add_client_billing_address" class="form-select form-select-sm" required>
                            <option value="">Select Address</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label required-field">Shipping Address</label>
                        <a type="button" class="my-primary-color ms-1" style="font-size: 11px;" data-bs-toggle="offcanvas" data-bs-target="#addressOffcanvas" data-target-select="add_client_shipping_address">
                            <i class="fa-solid fa-plus"></i> New Address
                        </a>
                        <select name="shipping_address_id" id="add_client_shipping_address" class="form-select form-select-sm" required>
                            <option value="">Select Address</option>
                        </select>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-sm bg-my-primary text-white">Save Client</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Address Offcanvas (New) --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="addressOffcanvas" style="z-index: 1070;">
        <div class="offcanvas-header py-2">
            <h5 class="offcanvas-title my-primary-color">Add New Address</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            @include('components.address.address-form', [
                'formId' => 'addAddressForm',
                'title'  => 'Add New Address',
                'offcanvasId' => 'addressOffcanvas',
            ])
        </div>
    </div>

    {{-- Edit Address Offcanvas --}}
    <div class="offcanvas offcanvas-end" id="editAddressOffcanvas" tabindex="-1" style="z-index: 1080;">
        <div class="offcanvas-header py-2">
            <h5 class="offcanvas-title my-primary-color">Edit Address</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            @include('components.address.address-form', [
                'formId' => 'editAddressForm',
                'title'  => 'Edit Existing Address',
                'offcanvasId' => 'editAddressOffcanvas',
            ])
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        window.products = @json($products);
        window.csrfToken = "{{ csrf_token() }}";
        window.cgst_division_percentage = "{{ \App\Models\Setting::get('cgst_division_percentage', 50) }}";
        window.sgst_division_percentage = "{{ \App\Models\Setting::get('sgst_division_percentage', 50) }}";
    </script>
    @vite('resources/js/pages/sales-order/invoice/show.js')
@endpush