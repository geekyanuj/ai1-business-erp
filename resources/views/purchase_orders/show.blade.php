    @extends('layouts.app')
    @section('title', 'Purchase Order Details')
    @section('content')
        <div class="container mt-2">

            {{-- ================= HEADER ================= --}}
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold my-primary-color">
                    <i class="fas fa-file-invoice me-2"></i>
                    {{ $purchaseOrder->po_number }}
                </h5>

                <div class="d-flex gap-2">
                    @if($purchaseOrder->status === 'draft')
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editPurchaseOrderModal">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    @endif

                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            {{-- ================= BREADCRUMB ================= --}}
            <div class="mb-3">
                <small class="text-muted">
                    <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
                    <i class="fa fa-angle-right"></i>
                    <a class="text-decoration-none" href="{{ route('purchase-orders.index') }}">Purchase Orders</a>
                    <i class="fa fa-angle-right"></i>
                    {{ $purchaseOrder->po_number }}
                </small>
            </div>

            {{-- ================= TABS ================= --}}
            <ul class="nav nav-tabs mb-2" id="purchaseOrderTabs">
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
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-supplier">
                        <i class="fas fa-industry"></i> Supplier
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-communication">
                        <i class="fas fa-message"></i> Communication
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-grns">
                        <i class="fa fa-box"></i> GRNs
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-logs">
                        <i class="fas fa-history"></i> Activity Log
                    </a>
                </li>
            </ul>

            {{-- ================= TAB CONTENT ================= --}}
            <div class="tab-content">

                {{-- ========== DETAILS TAB ========== --}}
                <div class="tab-pane fade show active" id="tab-details">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold text-secondary">Purchase Order Information</h6>
                            <hr>

                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>PO Number:</strong> {{ $purchaseOrder->po_number }}</p>
                                    <p><strong>PO Type:</strong>
                                        {{ $purchaseOrder->po_type === 'raw' ? 'Raw Material' : 'Ready Goods' }}
                                    </p>
                                    <p><strong>Quote Reference: </strong>
                                        {{ $purchaseOrder->quote_ref }}
                                    </p>
                                </div>

                                <div class="col-md-4">
                                    <p><strong>Order Date: </strong> {{ $purchaseOrder->ordered_date }}</p>
                                    <p><strong>Delivery Date: </strong>{{ $purchaseOrder->delivery_date ?? 'N/A' }}</p>
                                    @if ($purchaseOrder->received_date)
                                        <p><strong>Received Date: </strong>{{ $purchaseOrder->received_date ?? 'N/A' }}</p>
                                    @endif
                                </div>

                                <div class="col-md-4">
                                    <p>
                                        <strong>Status:</strong>
                                        <span class="badge
                                            @if($purchaseOrder->status === 'draft') bg-secondary
                                            @elseif($purchaseOrder->status === 'approved') bg-primary
                                            @elseif($purchaseOrder->status === 'partial') bg-success
                                            @elseif($purchaseOrder->status === 'received') bg-success
                                            @endif">
                                            {{ ucfirst($purchaseOrder->status) }}
                                        </span>
                                    </p>

                                    <p>
                                        <strong>Grand Total:</strong>
                                        ₹ {{ inr_format($purchaseOrder->grand_total) }}
                                    </p>
                                    @if ($purchaseOrder->received_by)
                                        <p><strong>Received By: </strong>{{ $purchaseOrder->receivedBy?->name }}</p>
                                    @endif
                                </div>

                                <div class="col-md-4">
                                    <strong>Deliver To:</strong>
                                    <p>
                                        @if($purchaseOrder->deliveryAddress)
                                            {{ $purchaseOrder->deliveryAddress->address_line_1 }}<br>
                                            @if($purchaseOrder->deliveryAddress->address_line_2) {{ $purchaseOrder->deliveryAddress->address_line_2 }}<br> @endif
                                            {{ $purchaseOrder->deliveryAddress->city }}, {{ $purchaseOrder->deliveryAddress->state }} - {{ $purchaseOrder->deliveryAddress->pincode }}<br>
                                            {{ $purchaseOrder->deliveryAddress->country }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>

                                <div class="col-md-4">

                                    <strong>Notes:</strong>
                                    <p>
                                        {!! nl2br(e($purchaseOrder->notes)) !!}
                                    </p>
                                </div>
                                <div class="col-md-4 w-75">

                                    <strong>Terms & Conditions:</strong>
                                    <p>
                                        {!! nl2br(e($purchaseOrder->tnc)) !!}
                                    </p>
                                </div>


                            </div>

                            @if($purchaseOrder->remarks)
                                <hr>
                                <p><strong>Remarks:</strong> {{ $purchaseOrder->remarks }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ========== ITEMS TAB ========== --}}
                <div class="tab-pane fade" id="tab-items">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold text-secondary">Ordered Items</h6>
                            <hr>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Item</th>
                                            <th>HSN</th>
                                            <th class="text-end">Qty</th>
                                            <th class="text-end">Rate</th>
                                            <th>UOM</th>
                                            <th class="text-end">Base Total</th>
                                            <th class="text-end">Tax (%)</th>
                                            <th class="text-end">Tax Amt</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchaseOrder->items as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <strong>{{ $item->product_name }}</strong>
                                                    @if($item->product_description)
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $item->product_description }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>{{ $item->hsn_code ?? '-' }}</td>
                                                <td class="text-end">{{ $item->quantity }}</td>
                                                <td class="text-end">{{ inr_format($item->unit_price) }}</td>
                                                <td>{{ $item->uom }}</td>
                                                <td class="text-end">
                                                    {{ inr_format($item->total) }}
                                                </td>
                                                <td class="text-end">
                                                    {{ inr_format($item->tax_rate) }}%
                                                </td>
                                                <td class="text-end">
                                                    {{ inr_format($item->tax_amount) }}
                                                </td>
                                                <td class="text-end fw-bold">
                                                    {{ inr_format($item->total_with_tax) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th colspan="9" class="text-end">Subtotal</th>
                                            <th class="text-end">
                                                ₹ {{ inr_format($purchaseOrder->subtotal) }}
                                            </th>
                                        </tr>

                                        @if($purchaseOrder->tax_type === 'cgst_sgst')
                                            <tr>
                                                <th colspan="9" class="text-end">CGST</th>
                                                <th class="text-end">
                                                    ₹ {{ inr_format($purchaseOrder->cgst_amount) }}
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="9" class="text-end">SGST</th>
                                                <th class="text-end">
                                                    ₹ {{ inr_format($purchaseOrder->sgst_amount) }}
                                                </th>
                                            </tr>
                                        @else
                                            <tr>
                                                <th colspan="9" class="text-end">IGST</th>
                                                <th class="text-end">
                                                    ₹ {{ inr_format($purchaseOrder->igst_amount) }}
                                                </th>
                                            </tr>
                                        @endif

                                        <tr class="table-success">
                                            <th colspan="9" class="text-end">Grand Total</th>
                                            <th class="text-end">
                                                ₹ {{ inr_format($purchaseOrder->grand_total) }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ========== SUPPLIER TAB ========== --}}
                <div class="tab-pane fade" id="tab-supplier">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold text-secondary">Supplier Information</h6>
                            <hr>

                            <p><strong>Name:</strong> {{ $purchaseOrder->supplier->name ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $purchaseOrder->supplier->email ?? 'N/A' }}</p>
                            <p><strong>Phone:</strong> {{ $purchaseOrder->supplier->phone ?? 'N/A' }}</p>
                            <p><strong>Address:</strong> {{ $purchaseOrder->supplier->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                {{-- COMMUNICATION TAB --}}
                <div class="tab-pane fade" id="tab-communication">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">

                            {{-- SEND EMAIL --}}
                            <form action="{{ route('purchase-orders.send-mail', $purchaseOrder->id) }}" method="POST">
                                @csrf

                                <div class="row g-2">

                                    {{-- FROM --}}
                                    <div class="col-md-4">
                                        <label class="form-label">From</label>
                                        <select name="from_email" class="form-select form-select-sm">
                                            <option value="info@tetechsolution.com">info@tetechsolution.com</option>
                                            <option value="sales@tetechsolution.com">sales@tetechsolution.com</option>
                                        </select>
                                    </div>

                                    {{-- TO --}}
                                    <div class="col-md-8">
                                        <label class="form-label">To</label>
                                        <input type="text" name="to" class="form-control form-control-sm"
                                            value="{{ $purchaseOrder->supplier->email }}">
                                        <small class="text-muted">Comma separated</small>
                                    </div>

                                    {{-- CC --}}
                                    <div class="col-md-12">
                                        <label class="form-label">CC</label>
                                        <input type="text" name="cc" class="form-control form-control-sm">
                                        <small class="text-muted">Comma separated</small>
                                    </div>

                                    {{-- SUBJECT --}}
                                    <div class="col-md-12">
                                        <label class="form-label">Subject</label>
                                        <input type="text" name="subject" class="form-control form-control-sm"
                                            value="Purchase Order {{ $purchaseOrder->po_number }}">
                                    </div>

                                    {{-- BODY --}}
                                    <div class="col-md-12">
                                        <label class="form-label">Message (Optional)</label>
                                        <textarea name="body" class="form-control form-control-sm small-text" rows="4"
                                            placeholder="Only add additional message here.">.</textarea>
                                    </div>

                                    <div class="col-md-12 text-end">
                                        <button class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Send Email
                                        </button>
                                    </div>

                                </div>
                            </form>

                            <hr>

                            {{-- EMAIL HISTORY --}}
                            <h6 class="fw-bold text-secondary">Email History</h6>

                            @forelse($purchaseOrder->communications as $mail)
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

                {{-- ========== GRN TAB ========== --}}
                <div class="tab-pane fade" id="tab-grns">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">

                            <div class="d-flex justify-content-between mb-2">
                                <h6 class="fw-bold text-secondary">Goods Receipt Notes</h6>

                                @if(in_array($purchaseOrder->status, ['approved', 'partial']) && ! $purchaseOrder->isFullyReceived())
                                    <button class="btn btn-success btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#grnModal">
                                        <i class="fa fa-plus"></i> Receive More
                                    </button>
                                @endif
                            </div>

                            @if($purchaseOrder->grns->count())
                                <table class="table table-bordered table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>GRN No</th>
                                            <th>Date</th>
                                            <th>Received By</th>
                                            <th class="text-end">Total Qty</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchaseOrder->grns as $grn)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $grn->grn_number }}</td>
                                                <td>{{ $grn->created_at->format('d-m-Y') }}</td>
                                                <td>{{ $grn->receiver?->name }}</td>
                                                <td class="text-end">
                                                    {{ $grn->items->sum('quantity_received') }}
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('grns.show', $grn->id) }}"
                                                    class="btn btn-outline-info btn-sm">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted">No GRNs generated yet.</p>
                            @endif

                        </div>
                    </div>
                </div>


                {{-- ========== ACTIVITY LOG TAB ========== --}}
                <div class="tab-pane fade" id="tab-logs">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold text-secondary">Activity Log</h6>
                            <hr>

                            @if($purchaseOrder->activityLogs->count())
                                <ul class="list-group list-group-flush">
                                    @foreach($purchaseOrder->activityLogs as $log)
                                        <li class="list-group-item">
                                            <strong>{{ $log->description }}</strong><br>
                                            <small class="text-muted">
                                                {{ $log->created_at->format('d-m-Y H:i') }}
                                                | {{ $log->causer->name ?? 'System' }}
                                            </small>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">No activity recorded.</p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            {{-- ================= ACTION BUTTONS ================= --}}
            <div class="d-flex gap-2 mb-4">
                @if($purchaseOrder->status === 'draft')
                    <form method="POST" action="{{ route('purchase-orders.approve', $purchaseOrder->id) }}">
                        @csrf
                        <button class="btn btn-success" onclick="return confirm('Approve this Purchase Order?')">
                            Approve
                        </button>
                    </form>
                @endif
                @if(
                    in_array($purchaseOrder->status, ['approved', 'partial'])
                    && ! $purchaseOrder->isFullyReceived()
                )
                    <button class="btn btn-success"
                            data-bs-toggle="modal"
                            data-bs-target="#grnModal">
                        <i class="fa fa-box"></i> Receive & Generate GRN
                    </button>
                @endif


                <a href="{{ route('purchase-orders.pdf', $purchaseOrder->id) }}" class="btn btn-dark">
                    <i class="fa fa-file-pdf"></i> View PDF
                </a>
            </div>

        </div>

        {{-- ================= EDIT MODAL ================= --}}

            <div class="modal fade" id="editPurchaseOrderModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">

                        <form action="{{ route('purchase-orders.update', $purchaseOrder->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="modal-header">
                                <h5 class="modal-title my-primary-color">
                                    Edit Purchase Order – {{ $purchaseOrder->po_number }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <div class="row g-3">

                                    {{-- PO Number --}}
                                    <div class="col-md-3">
                                        <label class="form-label">PO Number</label>
                                        <input type="text" class="form-control" value="{{ $purchaseOrder->po_number }}" disabled>
                                    </div>

                                    {{-- Order Date --}}
                                    <div class="col-md-3">
                                        <label class="form-label required-field">Order Date</label>
                                        <input type="date" name="ordered_date" class="form-control"
                                            value="{{ $purchaseOrder->ordered_date }}">
                                    </div>

                                    {{-- Delivery Date --}}
                                    <div class="col-md-3">
                                        <label class="form-label required-field">Delivery Date</label>
                                        <input type="date" name="delivery_date" class="form-control" id="delivery_date"
                                            value="{{ $purchaseOrder->delivery_date }}">
                                    </div>

                                    {{-- PO Type --}}
                                    <div class="col-md-3">
                                        <label class="form-label required-field">PO Type</label>
                                        <select name="po_type" class="form-select">
                                            <option value="raw" @selected($purchaseOrder->po_type == 'raw')>
                                                Raw Material
                                            </option>
                                            <option value="ready" @selected($purchaseOrder->po_type == 'ready')>
                                                Ready Goods
                                            </option>
                                        </select>
                                    </div>

                                    {{-- Quote Reference --}}
                                    <div class="col-md-3">
                                        <label class="form-label required-field">Quote Reference</label>
                                        <input type="text" class="form-control" name="quote_ref" value="{{ $purchaseOrder->quote_ref }}">
                                    </div>

                                    {{-- Supplier --}}
                                    <div class="col-md-4">
                                        <label class="form-label required-field">Supplier</label>
                                        <select name="supplier_id" class="form-select" id="supplier-dropdown">
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}"
                                                    @selected($supplier->id == $purchaseOrder->supplier_id)>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="row col-md-12">

                                        <!-- Deliver To -->
                                        <div class="col-md-3">
                                            <label class="form-label required-field">Deliver To Entity</label>
                                            <select name="deliver_to_entity_id" id="edit_deliver_to_entity_id" class="form-select deliver-to-entity-select" required>
                                                <option value="0" @selected($purchaseOrder->deliver_to_entity_id == 0)>My Company</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}" @selected($purchaseOrder->deliver_to_entity_id == $client->id)>
                                                        {{ $client->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label required-field">Deliver To Address</label>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a type="button" class="my-primary-color ms-1 action-add-new-address" style="font-size: 11px; display: none;"
                                                    data-bs-toggle="offcanvas" data-bs-target="#supplierAddressOffcanvas"
                                                    data-target-select="edit_deliver_to_id">
                                                    New
                                                </a>
                                                <a type="button" class="my-primary-color ms-1 edit-address-shortcut action-edit-address" style="font-size: 11px; display: none;"
                                                    data-bs-toggle="modal" data-bs-target="#editAddressModal"
                                                    data-select-id="edit_deliver_to_id">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </a>
                                            </div>
                                            <select name="deliver_to_id" id="edit_deliver_to_id" class="form-select" required>
                                                <option value="">Select Address</option>
                                                @if($purchaseOrder->deliver_to_id)
                                                    <option value="{{ $purchaseOrder->deliver_to_id }}" selected>
                                                        {{ $purchaseOrder->deliveryAddress->address_line_1 }}
                                                    </option>
                                                @endif
                                            </select>
                                        </div>

                                        {{-- Remarks --}}
                                        <div class="col-md-6">
                                            <label class="form-label">Remarks</label>
                                            <textarea name="remarks" class="form-control"
                                                rows="2">{{ $purchaseOrder->remarks }}</textarea>
                                        </div>
                                    </div>


                                    {{-- Items --}}
                                    <div class="col-12 mt-3">
                                        <h6 class="fw-bold">Items</h6>
                                        <table class="table table-bordered" id="itemsTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th width="">Item Name</th>
                                                    <th width="">Item Description</th>
                                                    <th width="">HSN</th>
                                                    <th width="70">Qty</th>
                                                    <th width="">Unit Price</th>
                                                    <th width="">UOM</th>
                                                    <th width="90">Tax Rate</th>
                                                    <th width="">Tax Amt</th>
                                                    <th width="">Total</th>
                                                    <th width="60" class="text-center">
                                                        <button type="button" class="btn btn-success btn-sm addItemRow">+</button>
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody class="po-items-body">
                                                @foreach($purchaseOrder->items as $item)
                                                    <tr>
                                                        <td class="serial text-center">{{ $loop->iteration }}</td>
                                                        <td>
                                                            <input type="text" name="items[product_name][]"
                                                                class="form-control form-control-sm product-name-input"
                                                                value="{{ $item->product_name }}" required>

                                                            <input type="hidden" name="items[product_id][]" class="product-id-input"
                                                                value="{{ $item->product_id }}">
                                                        </td>

                                                        <td>
                                                            <input type="text" name="items[product_description][]"
                                                                value="{{ $item->product_description }}"
                                                                class="form-control form-control-sm product-description-input">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="items[hsn_code][]"
                                                                class="form-control form-control-sm hsn_code-input"
                                                                value="{{ $item->hsn_code }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="items[quantity][]"
                                                                class="form-control form-control-sm qty" min="1"
                                                                value="{{ $item->quantity }}" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="items[unit_price][]"
                                                                class="form-control form-control-sm rate"
                                                                value="{{ $item->unit_price }}" step="0.01" min="0" required>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="items[uom][]" class="form-control form-control-sm"
                                                                value="{{ $item->uom }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="items[tax_rate][]"
                                                                class="form-control form-control-sm tax_rate" min="1" max="100"
                                                                value="{{ $item->tax_rate }}" required>
                                                        </td>

                                                        <td>
                                                            <input type="number" name="items[tax_amount][]"
                                                                class="form-control form-control-sm tax_amount" min="0"
                                                                value="{{ $item->tax_amount }}" readonly>
                                                        </td>

                                                        <td>
                                                            <input type="number" name="items[total_with_tax][]"
                                                                class="form-control form-control-sm total_with_tax" min="0"
                                                                value="{{ $item->total_with_tax }}" readonly>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- GST Summary --}}
                                    <div class="row mt-3">

                                        <div class="col-md-8">

                                            {{-- Notes --}}
                                            <div class="">
                                                <label class="form-label">Notes/Instructions</label>
                                                <textarea name="notes" class="form-control" style="font-size:0.9rem;"
                                                    rows="2">{{ $purchaseOrder->notes }}</textarea>
                                            </div>

                                            {{-- Terms and Conditions --}}
                                            <div class="">
                                                <label class="form-label"> Terms and Conditions </label>
                                                <textarea name="tnc" class="form-control" style="font-size:0.9rem;"
                                                    rows="3">{{ $purchaseOrder->tnc }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <table class="table table-bordered">
                                                <tbody class="po-totals-body">
                                                    <tr>
                                                        <th>Subtotal</th>
                                                        <td>
                                                            <input type="text" id="subtotal" name="subtotal" class="form-control"
                                                                value="{{ $purchaseOrder->subtotal }}" readonly>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <th>Tax Type</th>
                                                        <td>
                                                            <select id="tax_type" name="tax_type" class="form-select">
                                                                <option value="cgst_sgst"
                                                                    @selected($purchaseOrder->tax_type === 'cgst_sgst')>
                                                                    CGST + SGST
                                                                </option>
                                                                <option value="igst" @selected($purchaseOrder->tax_type === 'igst')>
                                                                    IGST
                                                                </option>
                                                            </select>
                                                        </td>
                                                    </tr>

                                                    <!-- <tr class="cgst-row">
                                                                                <th>CGST (%)</th>
                                                                                <td>
                                                                                    <input type="number" id="cgst_rate" name="cgst_rate"
                                                                                        class="form-control" value="{{ $purchaseOrder->cgst_rate }}">
                                                                                </td>
                                                                            </tr>

                                                                            <tr class="sgst-row">
                                                                                <th>SGST (%)</th>
                                                                                <td>
                                                                                    <input type="number" id="sgst_rate" name="sgst_rate"
                                                                                        class="form-control" value="{{ $purchaseOrder->sgst_rate }}">
                                                                                </td>
                                                                            </tr>

                                                                            <tr class="igst-row d-none">
                                                                                <th>IGST (%)</th>
                                                                                <td>
                                                                                    <input type="number" id="igst_rate" name="igst_rate"
                                                                                        class="form-control" value="{{ $purchaseOrder->igst_rate }}">
                                                                                </td>
                                                                            </tr> -->

                                                    <tr>
                                                        <th>Total Tax</th>
                                                        <td>
                                                            <input type="text" id="total_tax" class="form-control"
                                                                value="{{ $purchaseOrder->cgst_amount + $purchaseOrder->sgst_amount + $purchaseOrder->igst_amount }}"
                                                                readonly>
                                                        </td>
                                                    </tr>

                                                    <tr class="table-success">
                                                        <th>Grand Total</th>
                                                        <td>
                                                            <input type="text" id="grand_total" name="grand_total"
                                                                class="form-control" value="{{ $purchaseOrder->grand_total }}"
                                                                readonly>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">
                                    Update PO
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

        {{-- ================= GRN Modal ================= --}}
        <div class="modal fade" id="grnModal" tabindex="-1">
            <div class="modal-dialog modal-fullscreen p-4">
                <div class="modal-content rounded-2">

                    <form method="POST"
                        action="{{ route('purchase-orders.receive', $purchaseOrder->id) }}">
                        @csrf

                        {{-- HEADER --}}
                        <div class="modal-header">
                            <h5 class="modal-title">
                                GRN – Receive Purchase Order {{ $purchaseOrder->po_number }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        {{-- BODY --}}
                        <div class="modal-body">

                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Item</th>
                                        <th>Ordered Qty</th>
                                        <th>Remaining Qty</th>
                                        <th>Receive Qty</th>
                                        <th>Location</th>
                                        @if ($purchaseOrder->po_type ==='ready')
                                            <th>Product Mappings ({{  $company->company_code}})</th>
                                            <th>Supplier Serial Nos</th>
                                        @endif
                                    </tr>
                                </thead>

                                
                                <tbody>
                                    @foreach($purchaseOrder->items as $item)

                                        @php
                                            $receivedQty = $purchaseOrder->grns
                                                ->flatMap->items
                                                ->where('purchase_order_item_id', $item->id)
                                                ->sum('quantity_received');

                                            $remainingQty = max(0, $item->quantity - $receivedQty);
                                        @endphp

                                        <tr>
                                            <td>{{ $loop->iteration }}</td>

                                            <td><strong>{{ $item->product_name }}</strong></td>

                                            <td>{{ $item->quantity }}</td>

                                            <td>
                                                <span class="badge bg-info">{{ $remainingQty }}</span>
                                            </td>

                                            <td>
                                                {{-- REQUIRED --}}
                                                <input type="hidden"
                                                    name="items[{{ $item->id }}][purchase_order_item_id]"
                                                    value="{{ $item->id }}">

                                                <input type="number"
                                                    step="0.01"
                                                    min="0"
                                                    max="{{ $remainingQty }}"
                                                    data-remaining="{{ $remainingQty }}"
                                                    name="items[{{ $item->id }}][quantity_received]"
                                                    class="form-control quantity-received"
                                                    {{ $remainingQty <= 0 ? 'disabled' : '' }}>
                                            </td>

                                            <td>
                                                <select name="items[{{ $item->id }}][location]"
                                                        class="form-control" {{ $remainingQty <= 0 ? 'disabled' : '' }}>
                                                    <option value="">Select Location</option>
                                                    <option value="Main Warehouse">Main Warehouse</option>
                                                    <option value="Store">Store</option>
                                                </select>
                                            </td>
                                            @php
                                                $receivedItems = $purchaseOrder->grns
                                                    ->flatMap->items
                                                    ->where('purchase_order_item_id', $item->id);

                                                $mappedProductId = $receivedItems->first()?->product_id;
                                            @endphp

                                            @if ($purchaseOrder->po_type === 'ready')
                                                <td>
                                                    <select name="items[{{ $item->id }}][product_id]"
                                                            class="form-select product-map"
                                                            {{ $mappedProductId ? 'disabled' : '' }}>

                                                        <option value="">-- Map Product --</option>

                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}"
                                                                @selected($mappedProductId == $product->id)>
                                                                {{ $product->our_part_no }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    {{-- IMPORTANT: submit disabled value --}}
                                                    @if($mappedProductId)
                                                        <input type="hidden"
                                                            name="items[{{ $item->id }}][product_id]"
                                                            value="{{ $mappedProductId }}">
                                                    @endif
                                                
                                                </td>
                                                <td>
                                                    <textarea
                                                        name="items[{{ $item->id }}][supplier_serials]"
                                                        class="form-control"
                                                        placeholder="Enter one serial per line"
                                                        rows="3" {{ $remainingQty <= 0 ? 'disabled' : '' }}
                                                    ></textarea>

                                                </td>
                                            @endif

                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>

                            <div class="mt-3">
                                <label class="form-label">Remarks</label>
                                <textarea name="remarks"
                                        class="form-control"
                                        rows="2"></textarea>
                            </div>

                        </div>

                        {{-- FOOTER --}}
                        <div class="modal-footer">
                            <button type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">
                                Cancel
                            </button>

                            <button type="submit"
                                    class="btn btn-success"
                                    onclick="return confirm('Confirm GRN & update inventory?')">
                                <i class="fa fa-check"></i> Generate GRN
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>


    @endsection

    <!-- Offcanvas for Supplier Address -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="supplierAddressOffcanvas"
        aria-labelledby="supplierAddressOffcanvasLabel" style="z-index: 1070;">
        <div class="offcanvas-header py-2">
            <h5 class="offcanvas-title my-primary-color" id="supplierAddressOffcanvasLabel">Supplier Address</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            @include('components.address.address-form', [
                'formId' => 'supplierAddressForm',
                'title'  => 'Add New Address',
                'offcanvasId' => 'supplierAddressOffcanvas',
            ])
        </div>
    </div>

    <!-- Edit Address Modal -->
    <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true" style="z-index: 1080;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title my-primary-color" id="editAddressModalLabel">Edit Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('components.address.address-form', [
                        'formId' => 'editAddressForm',
                        'title'  => 'Edit Existing Address',
                        'offcanvasId' => '', // Not an offcanvas
                    ])
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
        <script>
            window.poTaxType = "{{ $purchaseOrder->tax_type }}";
            window.cgstRate = {{ $purchaseOrder->cgst_rate ?? 0 }};
            window.sgstRate = {{ $purchaseOrder->sgst_rate ?? 0 }};
            window.igstRate = {{ $purchaseOrder->igst_rate ?? 0 }};

            const el = document.getElementById("delivery_date");
            el && el.setAttribute("min", new Date().toISOString().split("T")[0]);
        </script>



        @vite('resources/js/pages/purchase-order/purchase-order-show.js')
    @endpush