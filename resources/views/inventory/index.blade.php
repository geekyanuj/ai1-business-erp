@extends('layouts.app')

@section('title', 'Inventory')
@push('styles')
<style>
.search-box {
    border-radius: 50px;
    overflow: hidden;
    background: #fff;
    border: 1px solid #dfe1e5;
    transition: box-shadow 0.2s ease, border-color 0.2s ease;
}
.search-box:hover,
.search-box:focus-within {
    border-color: transparent;
    box-shadow: 0 1px 6px rgba(32,33,36,.28);
}
.google-search {
    border: none;
    padding: 8px 6px;
    font-size: 14px;
}
.google-search:focus {
    box-shadow: none;
}
.search-icon {
    border: none;
    background: transparent;
    color: #9aa0a6;
    padding-left: 12px;
}
.clear-search {
    border: none;
    background: transparent;
    cursor: pointer;
    color: #70757a;
    padding-right: 12px;
    font-size: 14px;
}
.clear-search:hover { color: #000; }

.nav-tabs .nav-link.active {
    font-weight: 600;
}
.tab-badge {
    font-size: 11px;
    vertical-align: middle;
    margin-left: 4px;
}
</style>
@endpush

@section('content')
    <div class="container">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="mb-3">
                <h5 class="my-primary-color"><i class="fa-solid fa-boxes-stacked me-2"></i>Inventory</h5>
                <small class="text-muted">
                    <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
                    <i class="fa-solid fa-angle-right"></i> Inventory
                </small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('inventory.create') }}" class="btn btn-sm btn-outline-success">
                    <i class="fa fa-plus"></i> Add Item
                </a>
                <a href="{{ route('inventory.movements', ['inventory' => 1]) }}"
                   class="btn btn-sm btn-outline-secondary d-none"
                   id="viewMovementsBtn">
                    <i class="fa fa-exchange-alt"></i> Movements
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <div class="row mb-3 d-flex justify-content-between align-items-center">
            <div class="col-md-4">
                <div class="input-group search-box">
                    <span class="input-group-text search-icon">
                        <i class="fa fa-search"></i>
                    </span>
                    <input type="text" class="form-control google-search" id="universalSearch"
                        placeholder="Search Part No / Material / Item">
                    <span class="input-group-text clear-search d-none" id="clearSearch">✕</span>
                </div>
            </div>
            <div class="col-auto">
                <span class="badge bg-info text-dark me-1">RAW MATERIAL</span>
                <span class="badge bg-primary me-1">READY PRODUCT</span>
                <span class="badge bg-warning text-dark"><i class="fa fa-tools"></i> EQUIPMENT</span>
            </div>
        </div>

        {{-- Inventory Tabs --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <ul class="nav nav-tabs mb-3" id="inventoryTabs" role="tablist">

                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="ready-tab" data-bs-toggle="tab"
                            data-bs-target="#readyPane" type="button" role="tab" data-type="ready">
                            <i class="fa fa-box me-1"></i> Ready Products
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="raw-tab" data-bs-toggle="tab"
                            data-bs-target="#rawPane" type="button" role="tab" data-type="raw">
                            <i class="fa fa-industry me-1"></i> Raw Materials
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="equipment-tab" data-bs-toggle="tab"
                            data-bs-target="#equipmentPane" type="button" role="tab" data-type="equipment">
                            <i class="fa fa-tools me-1"></i> Equipment &amp; Tools
                        </button>
                    </li>

                </ul>

                <div class="tab-content">

                    {{-- READY PRODUCTS --}}
                    <div class="tab-pane fade show active" id="readyPane" role="tabpanel">
                        @include('inventory.partials.grid', ['type' => 'ready'])
                    </div>

                    {{-- RAW MATERIALS --}}
                    <div class="tab-pane fade" id="rawPane" role="tabpanel">
                        @include('inventory.partials.grid', ['type' => 'raw'])
                    </div>

                    {{-- EQUIPMENT & TOOLS --}}
                    <div class="tab-pane fade" id="equipmentPane" role="tabpanel">
                        @include('inventory.partials.grid', ['type' => 'equipment'])
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/inventory/inventory-index.js')
@endpush