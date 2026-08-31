@extends('layouts.app')

@section('title', 'Addresses')

@section('content')
    <div class="mb-3 d-flex justify-content-between align-items-end">
        <div>
            <h5 class="my-primary-color">Address Management</h5>
            <small class="text-muted">
                <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
                <i class="fa-solid fa-angle-right"></i> Addresses
            </small>
        </div>
        <button class="btn btn-sm bg-my-primary text-white" data-bs-toggle="modal" data-bs-target="#addAddressModal">
            <i class="fa fa-plus"></i> Add Address
        </button>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table id="addressesTable" class="table table-sm table-bordered table-striped pt-2 w-100">
                        <thead>
                            <tr>
                                <th>Address Line 1</th>
                                <th>Address Line 2</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Country</th>
                                <th>Postal Code</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Address Modal -->
    <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
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
                    <!-- Form submit button is inside the component -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add Address Modal -->
    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title my-primary-color" id="addAddressModalLabel">Add Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('components.address.address-form', [
                        'formId' => 'createAddressForm',
                        'title'  => 'Create New Address',
                        'offcanvasId' => '', // Not an offcanvas
                    ])
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <!-- Form submit button is inside the component -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/addresses-index.js')
@endpush
