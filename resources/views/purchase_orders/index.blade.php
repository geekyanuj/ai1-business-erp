@extends('layouts.app')
@section('title', 'Purchase Orders')

@section('content')
    <div class="mb-3">
        <h5 class="my-primary-color">Purchase Orders</h5>
        <small class="text-muted">
            <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
            <i class="fa-solid fa-angle-right"></i> Purchase Orders
        </small>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-end align-items-center gap-2 mb-3">

                <div class="add-po-container">
                    <a class="btn btn-sm btn-outline-info" href="{{ route('supplier.serial.search') }}">
                        <i class="fa-solid fa-search"></i> Search Supplier S/N
                    </a>
                </div>
                <div class="add-po-container">
                    <a class="btn btn-sm btn-outline-info" href="{{ route('grns.index') }}">
                        <i class="fa-solid fa-list"></i> GRNs
                    </a>
                </div>
                <div class="add-po-container">
                    <button class="btn bg-my-primary btn-sm text-white" data-bs-toggle="modal"
                        data-bs-target="#addPurchaseOrderModal">
                        <i class="fa-solid fa-plus"></i> New PO
                    </button>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-2">
                    <table id="poTable" data-url="{{ route('purchase-orders.data') }}"
                        class="table table-sm table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Type</th>
                                <th>Supplier</th>
                                <th>Order Date</th>
                                <th>Status</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Create PO Modal --}}
    <div class="modal fade" id="addPurchaseOrderModal" tabindex="-1" aria-labelledby="addPurchaseOrderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="addPurchaseOrderForm" action="{{ route('purchase-orders.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title my-primary-color" id="addPurchaseOrderModalLabel">Create Purchase Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            {{-- PO Number --}}
                            <div class="col-md-3">
                                <label class="form-label">PO Number</label>
                                <input type="text" class="form-control" id="createPONumber" value="" disabled
                                    data-url="{{ route('purchase-orders.generate-po') }}">
                                <input type="hidden" name="po_number" id="createPONumberHidden">
                            </div>
                            {{-- Order Date --}}
                            <div class="col-md-3">
                                <label class="form-label">Order Date</label>
                                <input type="date" class="form-control" name="ordered_date1" id="ordered_date1"
                                    value="{{ date('Y-m-d') }}" disabled>
                                <input type="hidden" class="form-control" name="ordered_date" id="ordered_date"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            {{-- Delivery Date --}}
                            <div class="col-md-3">
                                <label class="form-label required-field">Delivery Date</label>
                                <input type="date" name="delivery_date" class="form-control" id="delivery_date">
                            </div>

                            {{-- PO Type --}}
                            <div class="col-md-3">
                                <label class="form-label required-field">PO Type</label>
                                <select name="po_type" class="form-select" required>
                                    <option value="raw">Raw Material (Engineering)</option>
                                    <option value="ready">Ready Goods (Trading)</option>
                                </select>
                            </div>
                            {{-- PO Status --}}
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="draft" selected>Draft</option>
                                </select>
                            </div>
                            {{-- Quote Reference --}}
                            <div class="col-md-3">
                                <label class="form-label required-field">Quote Reference</label>
                                <input type="text" class="form-control" name="quote_ref">
                            </div>

                            {{-- Supplier --}}
                            <div class="col-md-3">
                                <label class="form-label required-field">Supplier</label>
                                <div class="input-group">
                                    <select name="supplier_id" id="supplier_id" class="form-select">
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <a type="button" class="my-primary-color ms-1" style="font-size: 12px;"
                                        data-bs-toggle="offcanvas" data-bs-target="#supplierOffcanvas">
                                        Add New Supplier
                                    </a>
                                </div>
                            </div>



                            <div class="row col-md-12">

                                <!-- Deliver To Entity -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Deliver To</label>
                                    <div class="input-group">
                                        <select id="deliver_to_entity_id" class="form-select">
                                            <option value="">Select Recipient</option>
                                            <option value="company">My Company</option>
                                            <optgroup label="Clients">
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}">
                                                        {{ $client->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-1" id="client_actions" style="display: none;">
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

                                <div class="col-md-6 mb-3">
                                    <label class="form-label required-field">Delivery Address</label>
                                    <select name="deliver_to_id" id="deliver_to_id" class="form-select" required>
                                        <option value="">Select Delivery Address</option>
                                    </select>
                                    <div class="d-flex align-items-center gap-2 mb-1" id="delivery_address_actions" style="display: none;">
                                        <a type="button" class="my-primary-color ms-1" style="font-size: 12px;"
                                            data-bs-toggle="offcanvas" data-bs-target="#supplierAddressOffcanvas"
                                            data-target-select="deliver_to_id">
                                            <i class="fa-solid fa-plus"></i> New
                                        </a>
                                        <a type="button" class="my-primary-color ms-1 edit-address-shortcut" style="font-size: 12px; display: none;"
                                            data-bs-toggle="offcanvas" data-bs-target="#editAddressOffcanvas"
                                            data-select-id="deliver_to_id">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>
                                    </div>
                                    
                                </div>

                                {{-- Remarks --}}
                                <div class="col-md-6">
                                    <label class="form-label">Remarks</label>
                                    <textarea class="form-control" rows="2" name="remarks"></textarea>
                                </div>
                            </div>

                            {{-- Items --}}
                            <div class="col-12 mt-3 card p-1">
                                <h6 class="fw-bold">Items</h6>

                                <table class="table table-bordered table-sm" id="itemsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th width="220">Item Name</th>
                                            <th width="220">Item Description</th>
                                            <th>HSN</th>
                                            <th width="">Qty</th>
                                            <th width="">Unit Price</th>
                                            <th width="">UOM</th>
                                            <th width="">Tax Rate</th>
                                            <th width="">Tax Amt</th>
                                            <th width="">Total</th>
                                            <th width="60" class="text-center">
                                                <button type="button" class="btn btn-success btn-sm"
                                                    id="addItemRow">+</button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="serial text-center">1</td>
                                            <td>
                                                <input type="text" name="items[product_name][]"
                                                    class="form-control form-control-sm product-name-input" required>
                                                <input type="hidden" name="items[product_id][]" class="product-id-input">
                                            </td>

                                            <td>
                                                <input type="text" name="items[product_description][]"
                                                    class="form-control form-control-sm product-description-input">
                                            </td>

                                            <td>
                                                <input type="text" name="items[hsn_code][]"
                                                    class="form-control form-control-sm hsn_code-input">
                                            </td>

                                            <td>
                                                <input type="number" name="items[quantity][]"
                                                    class="form-control form-control-sm qty" min="1" value="1" required>
                                            </td>

                                            <td>
                                                <input type="number" name="items[unit_price][]"
                                                    class="form-control form-control-sm rate" step="0.01" min="0" required>
                                            </td>

                                            <td>
                                                <input type="text" name="items[uom][]"
                                                    class="form-control form-control-sm uom">
                                            </td>

                                            <td>
                                                <input type="number" name="items[tax_rate][]"
                                                    class="form-control form-control-sm tax_rate" min="0" max="100" value="18" required>
                                            </td>

                                            <td>
                                                <input type="number" name="items[tax_amount][]"
                                                    class="form-control form-control-sm tax_amount" min="0" readonly>
                                            </td>

                                            <td>
                                                <input type="number" name="items[total_with_tax][]"
                                                    class="form-control form-control-sm total_with_tax" min="0" readonly>
                                            </td>

                                            <td class="text-center"><button type="button"
                                                    class="btn btn-danger btn-sm removeRow">X</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-8">

                                    {{-- Notes --}}
                                    <div class="">
                                        <label class="form-label">Notes/Instructions</label>
                                        <textarea name="notes" class="form-control" rows="8" id="notes-field"
                                            style="font-size:0.9rem"></textarea>
                                    </div>

                                    {{-- Terms and Conditions --}}
                                    <div class="">
                                        <label class="form-label"> Terms and Conditions </label>
                                        <textarea name="tnc" class="form-control" rows="3" id="tnc-field"
                                            style="font-size:0.9rem"></textarea>
                                    </div>
                                </div>

                                {{-- Total Summary --}}
                                <div class="col-md-4">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Subtotal</th>
                                            <td><input type="text" name="subtotal" id="subtotal" class="form-control"
                                                    readonly required></td>
                                        </tr>

                                        <tr>
                                            <th>Tax Type</th>
                                            <td>
                                                <select name="tax_type" id="tax_type" class="form-select">
                                                    <option value="cgst_sgst">CGST + SGST</option>
                                                    <option value="igst">IGST</option>
                                                </select>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Total Tax</th>
                                            <td><input type="text" id="total_tax" class="form-control" readonly></td>
                                        </tr>


                                        <tr>
                                            <th>Grand Total</th>
                                            <td><input type="text" id="grand_total" name="grand_total" class="form-control"
                                                    readonly required></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save PO</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>

                </form>

                {{-- Add Supplier Form --}}
                <div class="offcanvas offcanvas-end" tabindex="-1" id="supplierOffcanvas">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title">Add New Supplier</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="offcanvas-body">
                        <form id="addSupplierForm" data-url="{{ route('suppliers.ajax-store') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label required-field">Supplier Name</label>
                                <input type="text" class="form-control" placeholder="Supplier Name" id="supplier_name"
                                    name="name">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" placeholder="Phone" id="supplier_phone" name="phone"
                                    maxlength="10">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" placeholder="Email" id="supplier_email"
                                    name="email">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">GST No</label>
                                <input type="text" class="form-control" placeholder="GST No" id="supplier_gst"
                                    name="gst_number" maxlength="15">
                            </div>

                            <div class="mb-3">
                                <label class="form-label required-field">Address</label>
                                <a type="button" class="my-primary-color ms-1" style="font-size: 12px;"
                                    data-bs-toggle="offcanvas" data-bs-target="#supplierAddressOffcanvas"
                                    data-target-select="addSupplierAddress">
                                    Add New Address
                                </a>
                                <a type="button" class="my-primary-color ms-1 edit-address-shortcut" style="font-size: 12px; display: none;"
                                    data-bs-toggle="offcanvas" data-bs-target="#editAddressOffcanvas"
                                    data-select-id="addSupplierAddress">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <select name="address_id" id="addSupplierAddress" class="form-select form-select-sm" required>
                                    <option value="">Select Address</option>
                                </select>
                            </div>

                            <div class="text-end">
                                <button type="button" class="btn btn-sm bg-my-primary text-white" id="saveSupplierBtn"
                                    data-bs-dismiss="offcanvas">
                                    Save Supplier
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
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

    <!-- Edit Address Offcanvas -->
    <div class="offcanvas offcanvas-end offcanvas-top-layer" id="editAddressOffcanvas" tabindex="-1" style="z-index: 1080;">
        <div class="offcanvas-header py-2">
            <h5 class="offcanvas-title my-primary-color" id="editAddressOffcanvasLabel">Edit Address</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            @include('components.address.address-form', [
                'formId' => 'editAddressForm',
                'title'  => 'Edit Existing Address',
                'offcanvasId' => 'editAddressOffcanvas', 
            ])
        </div>
    </div>
    <!-- Edit Client Offcanvas -->
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
                        <a type="button" class="my-primary-color ms-1" style="font-size: 11px;" data-bs-toggle="offcanvas" data-bs-target="#supplierAddressOffcanvas" data-target-select="add_client_billing_address">
                            <i class="fa-solid fa-plus"></i> New Address
                        </a>
                        <select name="billing_address_id" id="add_client_billing_address" class="form-select form-select-sm" required>
                            <option value="">Select Address</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label required-field">Shipping Address</label>
                        <a type="button" class="my-primary-color ms-1" style="font-size: 11px;" data-bs-toggle="offcanvas" data-bs-target="#supplierAddressOffcanvas" data-target-select="add_client_shipping_address">
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

@push('scripts')
    <script>
        window.generatePOUrl = "{{ route('purchase-orders.generate-po') }}";


        const today = new Date().toISOString().split('T')[0];
        document.getElementById("delivery_date").setAttribute("min", today);


        //put the pre-defined data into the notes and tnc
        document.getElementById('notes-field').value =
            'Looking forward to future business opportunities.';

        document.getElementById('tnc-field').value =
            '• Delivery must be on or before agreed date.\n' +
            '• Invoice must clearly mention PO number.\n' +
            '• GST Invoice is mandatory for payment processing.';
    </script>
    @vite('resources/js/pages/purchase-orders-index.js')
@endpush