@extends('layouts.app')
@section('title', 'Production Lots')

@section('content')
<div class="container-fluid px-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="mb-3">
            <h5 class="my-primary-color"><i class="fa fa-industry me-2"></i>Production Lots</h5>
            <small class="text-muted">
                <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
                <i class="fa-solid fa-angle-right"></i> Production Lots
            </small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('production.batches.create') }}" class="btn btn-sm btn-outline-success">
                <i class="fa fa-plus"></i> New Production Lot
            </a>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Table --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover w-100" id="batchesTable" 
                   data-url="{{ route('production.batches.datatable') }}">
                <thead class="table-light">
                    <tr>
                        <th width="150">Lot No</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th width="120">Quantity</th>
                        <th>Client</th>
                        <th>Serial No.</th>
                        <th>Notes</th>
                        <th width="150">Production Date</th>
                        <th>Source</th>
                        <th width="120">Status</th>
                        <th width="100" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    @vite('resources/js/pages/production/batches-index.js')
@endpush
