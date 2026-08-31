@extends('layouts.app')
@section('title', 'Suppliers')

@section('content')

    <div class="mb-2 d-flex justify-content-between align-items-end">
        <div>
            <h5 class="my-primary-color">Suppliers</h5>
            <small class="text-muted">
                <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
                <i class="fa fa-angle-right"></i> Suppliers
            </small>
        </div>
        <button class="btn btn-sm bg-my-primary text-white" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
            <i class="fa fa-plus"></i> Add Supplier
        </button>
    </div>

    <div class="card">
        <div class="card-body py-2">
            <table id="suppliersTable" data-url="{{ route('suppliers.data') }}"
                class="table table-sm table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>GST</th>
                        <th>Supplier Since</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- ADD MODAL --}}
    <div class="modal fade" id="addSupplierModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('suppliers.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h6>Add Supplier</h6>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label required-field">Supplier Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Supplier Name" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" placeholder="Phone">
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" placeholder="Email">
                        </div>

                        <div class="mb-2">
                            <label class="form-label">GST No</label>
                            <input type="text" class="form-control" name="gst_number" placeholder="GST Number">
                        </div>

                        <div class="mb-2">
                            <label class="form-label required-field">Address</label>
                            <a type="button" class="my-primary-color ms-1" style="font-size: 12px;"
                                data-bs-toggle="offcanvas" data-bs-target="#supplierAddressOffcanvas"
                                data-target-select="addSupplierAddress">
                                Add New Address
                            </a>
                            <select name="address_id" id="addSupplierAddress" class="form-select form-select-sm" required>
                                <option value="">Select Address</option>
                            </select>
                        </div>
                    </div>


                    <div class="modal-footer">
                        <button class="btn btn-primary btn-sm">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT MODAL (SINGLE, REUSABLE) --}}
    <div class="modal fade" id="editSupplierModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="editSupplierForm">
                @csrf
                @method('PUT')

                <div class="modal-content">
                    <div class="modal-header">
                        <h6>Edit Supplier</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-2">
                            <label for="editName" class="form-label required-field">Supplier Name</label>
                            <input type="text" class="form-control" name="name" id="editName" required>
                        </div>

                        <div class="mb-2">
                            <label for="editPhone" class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" id="editPhone">
                        </div>

                        <div class="mb-2">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="editEmail">
                        </div>

                        <div class="mb-2">
                            <label for="editGst" class="form-label">GST No</label>
                            <input type="text" class="form-control" name="gst_number" id="editGst">
                        </div>

                        <div class="mb-2">
                            <label for="editAddress" class="form-label required-field">Address</label>
                            <a type="button" class="my-primary-color ms-1" style="font-size: 12px;"
                                data-bs-toggle="offcanvas" data-bs-target="#supplierAddressOffcanvas"
                                data-target-select="editSupplierAddress">
                                Add New Address
                            </a>
                            <select name="address_id" id="editSupplierAddress" class="form-select form-select-sm" required>
                                <option value="">Select Address</option>
                            </select>
                        </div>
                    </div>


                    <div class="modal-footer">
                        <button class="btn btn-primary btn-sm">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

    <!-- Offcanvas for Supplier Address -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="supplierAddressOffcanvas"
        aria-labelledby="supplierAddressOffcanvasLabel">
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
@push('scripts')
    @vite('resources/js/pages/suppliers-index.js')
@endpush