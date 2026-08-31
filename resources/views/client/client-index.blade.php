@extends('layouts.app')
@section('title')
    Clients
@endsection

@section('content')
    <div class="mb-3">
        <h5 class="my-primary-color">Clients</h5>
        <small class="text-muted">
            <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
            <i class="fa-solid fa-angle-right"></i> Clients
        </small>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="user-action">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"></h5>
                    <div class="add-user-container">
                        <button class="btn btn-sm text-white bg-my-primary" data-bs-toggle="modal"
                            data-bs-target="#addClientModal"><i class="fa-solid fa-plus"></i> Add New Client</button>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body  my-0 py-0">
                        <table id="clientsTable" data-url="{{ route('clients.data') }}"
                            class="table table-sm table-small table-bordered table-striped pt-2">
                            <thead>
                                <tr>
                                    <th>Client Name</th>
                                    <th>Contact Person</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Client Since</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>


            </div>
        </div>


        <!-- Add Clent Modal -->
        <div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <form id="addClientForm" action="{{ route('clients.store') }}" method="POST">
                        @csrf

                        <div class="modal-header py-2">
                            <h5 class="modal-title my-primary-color" id="addClientModalLabel">Add New Client</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <!-- Client Name -->
                                <div class="col-md-12">
                                    <label for="name" class="form-label mb-1 required-field">Client Name</label>
                                    <input type="text" name="name" id="name" class="form-control form-control-sm" required>
                                </div>

                                <!-- Contact Person -->
                                <div class="col-md-6">
                                    <label for="contactPerson" class="form-label mb-1 required-field">Contact Person
                                    </label>
                                    <input type="text" name="contact_person" id="contactPerson"
                                        class="form-control form-control-sm" required>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label mb-1 required-field">Email</label>
                                    <input type="email" name="email" id="email" class="form-control form-control-sm"
                                        required>
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label for="phone" class="form-label mb-1">Phone</label>
                                    <input type="text" name="phone" id="phone" class="form-control form-control-sm">
                                </div>

                                <!-- GST -->
                                <div class="col-md-6">
                                    <label for="gstNumber" class="form-label mb-1">GST Number (Optional)</label>
                                    <input type="text" name="gst_number" id="gstNumber"
                                        class="form-control form-control-sm">
                                </div>

                                <!-- Billing Address -->
                                <div class="col-md-12">
                                    <label for="addBillingAddress" class="form-label mb-1 required-field">Billing Address</label>
                                    <a type="button" class="my-primary-color ms-1" style="font-size: 12px;"
                                        data-bs-toggle="offcanvas" data-bs-target="#billingAddressOffcanvas"
                                        data-target-select="addBillingAddress">
                                        Add New Address
                                    </a>
                                    <select name="billing_address_id" id="addBillingAddress"
                                        class="form-select form-select-sm" required>
                                        <option value="">Select Billing Address</option>
                                    </select>
                                </div>

                                <!-- Shipping Address -->
                                <div class="col-md-12">
                                    <label for="addShippingAddress" class="form-label mb-1 required-field">Shipping Address</label>
                                    <a type="button" class="my-primary-color ms-1" style="font-size: 12px;"
                                        data-bs-toggle="offcanvas" data-bs-target="#shippingAddressOffcanvas"
                                        data-target-select="addShippingAddress">
                                        Add New Address
                                    </a>
                                    <select name="shipping_address_id" id="addShippingAddress"
                                        class="form-select form-select-sm" required>
                                        <option value="">Select Shipping Address</option>
                                    </select>
                                </div>

                                <!-- Notes -->
                                <div class="col-md-12">
                                    <label for="notes" class="form-label mb-1">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control form-control-sm"
                                        rows="3"></textarea>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer py-2">
                            <button type="button" class="btn btn-secondary btn-sm px-3"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm px-3">
                                <i class="fas fa-save me-1"></i> Save Client
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>



        <!-- Edit Client Modal -->
        <div class="modal fade" id="editClientModal" data-bs-backdrop="static" tabindex="-1"
            aria-labelledby="editClientModalLabel" aria-hidden="true">

            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="editClientForm" action="" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-header py-2">
                            <h5 class="modal-title my-primary-color" id="editClientModalLabel">Edit Client</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">

                                <input type="hidden" name="id" id="editClientId">

                                <!-- Client Name -->
                                <div class="col-md-12">
                                    <label class="form-label mb-1 required-field">Client Name</label>
                                    <input type="text" name="name" id="editClientName" class="form-control form-control-sm"
                                        required>
                                </div>

                                <!-- Contact Person -->
                                <div class="col-md-6">
                                    <label class="form-label mb-1 required-field">Contact Person</label>
                                    <input type="text" name="contact_person" id="editClientContactPerson"
                                        class="form-control form-control-sm" required>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label class="form-label mb-1 required-field">Email</label>
                                    <input type="email" name="email" id="editClientEmail"
                                        class="form-control form-control-sm" required>
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label for="editClientPhone" class="form-label mb-1">Phone</label>
                                    <input type="text" name="phone" id="editClientPhone"
                                        class="form-control form-control-sm">
                                </div>

                                <!-- GST -->
                                <div class="col-md-6">
                                    <label class="form-label mb-1">GST Number (Optional)</label>
                                    <input type="text" name="gst_number" id="editClientGstNumber"
                                        class="form-control form-control-sm">
                                </div>

                                <!-- Billing Address -->
                                <div class="col-md-12">
                                    <label class="form-label mb-1 required-field">Billing Address</label>
                                    <a type="button" class="my-primary-color ms-1" style="font-size: 12px;"
                                        data-bs-toggle="offcanvas" data-bs-target="#billingAddressOffcanvas"
                                        data-target-select="editClientBillingAddress">
                                        Add New Address
                                    </a>
                                    <select name="billing_address_id" id="editClientBillingAddress"
                                        class="form-select form-select-sm" required>
                                        <option value="">Select Billing Address</option>
                                    </select>
                                </div>

                                <!-- Shipping Address -->
                                <div class="col-md-12">
                                    <label class="form-label mb-1 required-field">Shipping Address</label>
                                    <a type="button" class="my-primary-color ms-1" style="font-size: 12px;"
                                        data-bs-toggle="offcanvas" data-bs-target="#shippingAddressOffcanvas"
                                        data-target-select="editClientShippingAddress">
                                        Add New Address
                                    </a>
                                    <select name="shipping_address_id" id="editClientShippingAddress"
                                        class="form-select form-select-sm" required>
                                        <option value="">Select Shipping Address</option>
                                    </select>
                                </div>

                                <!-- Notes -->
                                <div class="col-md-12">
                                    <label class="form-label mb-1">Notes</label>
                                    <textarea name="notes" id="editClientNotes" class="form-control form-control-sm"
                                        rows="3"></textarea>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer py-2">
                            <button type="button" class="btn btn-secondary btn-sm px-3"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary btn-sm px-3">Update Client</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <!-- Offcanvas for Billing Address -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="billingAddressOffcanvas"
        aria-labelledby="billingAddressOffcanvasLabel">
        <div class="offcanvas-header py-2">
            <h5 class="offcanvas-title my-primary-color" id="billingAddressOffcanvasLabel">Billing Address</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            @include('components.address.address-form', [
                'formId' => 'billingAddressForm',
                'title'  => 'Add New Billing Address',
                'offcanvasId' => 'billingAddressOffcanvas',
            ])
        </div>
    </div>

    <!-- Offcanvas for Shipping Address -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="shippingAddressOffcanvas"
        aria-labelledby="shippingAddressOffcanvasLabel">
        <div class="offcanvas-header py-2">
            <h5 class="offcanvas-title my-primary-color" id="shippingAddressOffcanvasLabel">Shipping Address</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            @include('components.address.address-form', [
                'formId' => 'shippingAddressForm',
                'title'  => 'Add New Shipping Address',
                'offcanvasId' => 'shippingAddressOffcanvas',
            ])
        </div>
    </div>

@endsection

    @push('scripts')
        @vite('resources/js/pages/clients-index.js')
    @endpush