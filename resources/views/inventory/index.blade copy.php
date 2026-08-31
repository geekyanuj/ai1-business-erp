@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
    <div class="container">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="mb-3">
                <h5 class="my-primary-color"> Inventory</h5>
                <small class="text-muted">
                    <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
                    <i class="fa-solid fa-angle-right"></i> Inventory
                </small>
            </div>
        </div>

        {{-- Filters --}}
        <div class="row mb-3">
            <div class="col-md-3">
                <select class="form-select form-select-sm" id="inventoryType">
                    <option value="">All Inventory</option>
                    <option value="ready">Ready Products</option>
                    <option value="raw">Raw Materials</option>
                </select>
            </div>

            <div class="col-md-3">
                <input type="text" class="form-control form-control-sm"
                    placeholder="Search Part No / Description"
                    id="inventorySearch">
            </div>

            <div class="col-md-3">
                <select class="form-select form-select-sm" id="locationFilter">
                    <option value="">All Locations</option>
                    <option value="main warehouse">Main Warehouse</option>
                    <option value="store">Store</option>
                </select>
            </div>

            <!-- <div class="col-md-3 text-end">
                <a href="{{ route('inventory.create') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus"></i> Add Stock
                </a>
            </div> -->
        </div>



        {{-- Inventory Table --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle w-100" id="inventoryTable"
                        data-url="{{ route('inventory.datatable') }}">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Part No</th>
                                <th>Material</th>
                                <th>Location</th>
                                <th>Available Qty</th>
                                <th>Reserved Qty</th>
                                <th>Free Stock</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- DataTables --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/inventory/inventory-index.js')
@endpush