@extends('layouts.app')
@section('title', 'BOM Master')

@section('content')
<div class="container-fluid px-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="mb-3">
            <h5 class="my-primary-color"><i class="fa fa-sitemap me-2"></i>BOM Master</h5>
            <small class="text-muted">
                <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
                <i class="fa-solid fa-angle-right"></i> BOM Master
            </small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('production.boms.create') }}" class="btn btn-sm btn-outline-success">
                <i class="fa fa-plus"></i> New BOM
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
            <table class="table table-bordered table-hover w-100" id="bomsTable"
                   data-url="{{ route('production.boms.datatable') }}">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th width="150">Version</th>
                        <th width="150">Status</th>
                        <th width="120" class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    @vite('resources/js/pages/production/boms-index.js')
@endpush
