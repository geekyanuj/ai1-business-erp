@extends('layouts.app')

@section('title', 'Inventory Details')

@section('content')
<div class="container">

    {{-- Header + Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="my-primary-color mb-0">Inventory Details</h5>
            <small class="text-muted">
                <a href="{{ route('inventory.index') }}">Inventory</a>
                <i class="fa-solid fa-angle-right mx-1"></i>
                View
            </small>
        </div>

        <div class="btn-group">
            <a href="{{ route('inventory.index') }}"
               class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-arrow-left"></i> Back
            </a>

            <a href="{{ route('inventory.movements', $inventory->id) }}"
               class="btn btn-sm btn-outline-primary">
                <i class="fa fa-list"></i> Movements
            </a>

            <a href="{{ route('inventory.adjust', $inventory->id) }}"
               class="btn btn-sm btn-outline-warning">
                <i class="fa fa-sliders"></i> Adjust
            </a>

            <button class="btn btn-sm btn-outline-success"
                    data-bs-toggle="modal"
                    data-bs-target="#grnModal">
                <i class="fa fa-truck"></i> GRN
            </button>
        </div>
    </div>

    {{-- Inventory Summary --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-3 small">

                @if ($inventory->product)
                    <div class="col-md-4">
                        <strong>Part No</strong><br>
                        {{ $inventory->product?->our_part_no ?? '-' }}
                    </div>
                @endif

                @if (!$inventory->product)
                    <div class="col-md-4">
                        <strong>Material Name</strong><br>
                        {{ $inventory->material_name }}
                    </div>
                @endif

                <div class="col-md-4">
                    <strong>Location</strong><br>
                    {{ $inventory->location ?? '—' }}
                </div>

                <div class="col-md-3">
                    <strong>Item Type</strong><br>
                    <span class="badge bg-info text-dark">
                        {{ strtoupper($inventory->inventory_type) }}
                    </span>
                </div>

                <div class="col-md-3">
                    <strong>Available Qty</strong><br>
                    <span class="fw-bold text-success">
                        {{ $inventory->quantity_available }}
                    </span>
                </div>

                <div class="col-md-3">
                    <strong>Reserved Qty</strong><br>
                    <span class="fw-bold text-warning">
                        {{ $inventory->quantity_reserved }}
                    </span>
                </div>

                <div class="col-md-3">
                    <strong>Free Stock</strong><br>
                    <span class="fw-bold text-primary">
                        {{ $inventory->quantity_available - $inventory->quantity_reserved }}
                    </span>
                </div>

            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card border-success shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Available</h6>
                    <h4 class="text-success mb-0">
                        {{ $inventory->quantity_available }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-warning shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Reserved</h6>
                    <h4 class="text-warning mb-0">
                        {{ $inventory->quantity_reserved }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-primary shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-1">Free Stock</h6>
                    <h4 class="text-primary mb-0">
                        {{ $inventory->quantity_available - $inventory->quantity_reserved }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Serial Numbers --}}
    @if($inventory->serialNumbers->count())
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Serial Numbers</h6>
            <span class="badge bg-secondary">
                {{ $inventory->serialNumbers->count() }} Items
            </span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Serial Number</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventory->serialNumbers as $sn)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $sn->serial_number }}</td>
                            <td>
                                <span class="badge 
                                    @if($sn->status === 'in_stock') bg-success
                                    @elseif($sn->status === 'reserved') bg-warning
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst($sn->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>

{{-- GRN Modal Placeholder --}}
<div class="modal fade" id="grnModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Generate GRN</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-muted">
                GRN generation UI goes here.
            </div>
        </div>
    </div>
</div>

@endsection
