@extends('layouts.app')
@section('title')
    Quotations
@endsection

@section('content')
    <div class="mb-3">
        <h5 class="my-primary-color">Quotations</h5>
        <small class="text-muted">
            <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
            <i class="fa-solid fa-angle-right"></i> Sales Orders
            <i class="fa-solid fa-angle-right"></i> Quotations
        </small>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="filterIcon btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
                        <i class="fa-solid fa-filter"></i>
                        <span class="filter-text">Filter</span>
                    </div>
                    <div id="filterContainer" class="filter-container">
                        <div class="filter1">
                            <select id="clientFilter" class="form-select">
                                <option value="">Select Client</option>
                                @foreach ($clients as $c)
                                    <option value="{{ $c->name }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter2">
                            <select id="statusFilter" class="form-select" style="">
                                <option value="">All Status</option>
                            </select>
                        </div>

                    </div>

                </div>
                <div class="add-salesorder-container">

                    <button class="btn bg-my-primary btn-sm text-white" data-bs-toggle="modal"
                        data-bs-target="#addQuotationModal">
                        <i class="fa-solid fa-plus"></i> New Quotation</button>
                    <button class="btn btn-success btn-sm" id="exportBtn"><i class="fa-solid fa-file-export"></i>
                        Export Excel</button>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-2">
                    <table id="orderTable" data-url="{{ route('sales-orders.quotations.data') }}"
                        class="table table-sm table-bordered table-striped pt-2">
                        <thead>
                            <tr>
                                <th>Quotation Number</th>
                                <th>Client Name</th>
                                <th>Quotation Date</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>View</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <!-- Add Quotation Modal -->
    <div class="modal fade" id="addQuotationModal" tabindex="-1" aria-labelledby="addQuotationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="addQuotationForm" action="{{ route('quotations.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title my-primary-color" id="addQuotationModalLabel">Create New Quotation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">

                            <div class="col-md-3">
                                <label for="addQuotationDate" class="form-label">Quotation Date</label>
                                <!-- Visible but disabled -->
                                <input type="text" class="form-control" id="addQuotationDate" value="{{ date('d-m-Y') }}"
                                    disabled>
                                <!-- Hidden field for submission -->
                                <input type="hidden" name="quotation_date" id="addQuotationDateHidden"
                                    value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-md-3">
                                <label for="addQuotationNumber" class="form-label">Quotation No. </label>
                                <input type="text" class="form-control" id="addQuotationNumber" value="" data-url={{ route('sales-orders.quotations.generate-quotation-number') }} disabled>
                                <input type="hidden" id="addQuotationNumberHidden" name="quotation_number">
                            </div>

                            <div class="col-md-3">
                                <label for="addQuotationStatus" class="form-label required-field">Current Status</label>
                                <select name="status" id="addQuotationStatus" class="form-select" required>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="addClientName" class="form-label required-field">Client Name</label>
                                <select name="client_id" id="addClientName" class="form-select">
                                    <option value="">Select Client</option>
                                    @foreach ($clients as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <a type="button" class="my-primary-color" style="font-size: 12px;"
                                        data-bs-toggle="offcanvas" data-bs-target="#clientOffcanvas">
                                        <i class="fa-solid fa-plus"></i> Add New Client
                                    </a>
                                    <a type="button" class="my-primary-color edit-client-shortcut" style="font-size: 12px; display: none;"
                                        data-bs-toggle="offcanvas" data-bs-target="#editClientOffcanvas">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit Client
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required-field">Billing Address</label>
                                <select name="billing_address_id" id="billing_address_id" class="form-select" required>
                                    <option value="">Select Billing Address</option>
                                </select>
                                <div class="d-flex align-items-center gap-2 mt-1 address-actions" style="display: none;">
                                    <a type="button" class="my-primary-color" style="font-size: 12px;"
                                        data-bs-toggle="offcanvas" data-bs-target="#addressOffcanvas" data-target-select="billing_address_id">
                                        <i class="fa-solid fa-plus"></i> New
                                    </a>
                                    <a type="button" class="my-primary-color edit-address-shortcut" style="font-size: 12px; display: none;"
                                        data-bs-toggle="offcanvas" data-bs-target="#editAddressOffcanvas" data-select-id="billing_address_id">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label required-field">Shipping Address</label>
                                <select name="shipping_address_id" id="shipping_address_id" class="form-select" required>
                                    <option value="">Select Shipping Address</option>
                                </select>
                                <div class="d-flex align-items-center gap-2 mt-1 address-actions" style="display: none;">
                                    <a type="button" class="my-primary-color" style="font-size: 12px;"
                                        data-bs-toggle="offcanvas" data-bs-target="#addressOffcanvas" data-target-select="shipping_address_id">
                                        <i class="fa-solid fa-plus"></i> New
                                    </a>
                                    <a type="button" class="my-primary-color edit-address-shortcut" style="font-size: 12px; display: none;"
                                        data-bs-toggle="offcanvas" data-bs-target="#editAddressOffcanvas" data-select-id="shipping_address_id">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Client Query From</label>
                                <input type="text" name="client_query_from" class="form-control">
                            </div>


                            <div class="col-md-12 mt-3">
                                <label class="form-label">Order Items</label>

                                <table class="table table-bordered" id="itemsTable">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Rate</th>
                                            <th>Discount (%)</th>
                                            <th>Taxable</th>
                                            <th>Tax (%)</th>
                                            <th>Tax</th>
                                            <th>Total</th>
                                            <th width="50">
                                                <button type="button" class="btn btn-success btn-sm" id="addItemRow"
                                                    data-url={{ route('products.list') }}>
                                                    +
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select name="items[0][product_id]" class="product-dropdown form-select"
                                                    id="product-dropdown" required>
                                                    <option value="">Select</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}">
                                                            {{ $product->our_part_no }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <td><input type="number" name="items[0][quantity]" class="form-control qty"
                                                    min="1" value="1"></td>

                                            <td><input type="number" name="items[0][unit_price]" class="form-control rate"
                                                    step="0.01" min="0"></td>

                                            <td><input type="number" name="items[0][discount_percent]"
                                                    class="form-control discount_percent" step="0.01" min="0"> </td>

                                            <td>
                                                <input type="text" class="form-control taxable_amount" readonly>
                                            </td>

                                            <td>
                                                <input type="number" name="items[0][tax_rate]" class="form-control tax_rate"
                                                    step="0.01" min="0">
                                            </td>

                                            <td>
                                                <input type="text" class="form-control tax_amount" readonly>
                                            </td>

                                            <td>
                                                <input type="text" class="form-control total_with_tax" readonly>
                                            </td>

                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
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
                                        <textarea name="notes" class="form-control" rows="3" id="notes-field"
                                            style="font-size:0.9rem"></textarea>
                                    </div>

                                    {{-- Terms and Conditions --}}
                                    <div class="">
                                        <label class="form-label"> Terms and Conditions </label>
                                        <textarea name="tnc" class="form-control" rows="3" id="tnc-field"
                                            style="font-size:0.9rem"></textarea>
                                    </div>
                                </div>

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

                                        <tr class="tax-row">
                                            <th>Total Tax</th>
                                            <td><input type="text" id="total_tax" class="form-control" readonly></td>
                                        </tr>

                                        <tr class="cgst-sgst-row">
                                            <th id="cgst_label">CGST</th>
                                            <td><input type="text" id="cgst_amount" class="form-control" readonly></td>
                                        </tr>
                                        <tr class="cgst-sgst-row">
                                            <th id="sgst_label">SGST</th>
                                            <td><input type="text" id="sgst_amount" class="form-control" readonly></td>
                                        </tr>
                                        <tr class="igst-row" style="display:none;">
                                            <th id="igst_label">IGST</th>
                                            <td><input type="text" id="igst_amount" class="form-control" readonly></td>
                                        </tr>

                                        <tr>
                                            <th>Grand Total</th>
                                            <td><input type="text" id="grand_total" name="grand_total" class="form-control"
                                                    readonly required></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label for="addSalesOrderRemarks" class="form-label">Remarks </label>
                                <textarea class="form-control" id="addSalesOrderRemarks" name="remarks" cols="3"></textarea>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Quotation
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
                {{-- Edit Client Offcanvas --}}
                <div class="offcanvas offcanvas-end" tabindex="-1" id="editClientOffcanvas">
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
                <div class="offcanvas offcanvas-end" tabindex="-1" id="clientOffcanvas">
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
            </div>
        </div>


    </div>


@endsection

@push('scripts')
    <script>
        window.products = @json($products);
        window.clientsData = @json($clients);
        window.branchState = "{{ $branchState }}";
        window.cgst_division_percentage = "{{ \App\Models\Setting::get('cgst_division_percentage', 50) }}";
        window.sgst_division_percentage = "{{ \App\Models\Setting::get('sgst_division_percentage', 50) }}";
    </script>
    @vite('resources/js/pages/sales-order/quotation/index.js')
@endpush