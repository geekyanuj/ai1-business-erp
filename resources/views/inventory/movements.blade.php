@extends('layouts.app')

@section('title', 'Inventory Movements')

@section('content')
<div class="container">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="my-primary-color mb-0">Inventory Movements</h5>
            <small class="text-muted">
                <a href="{{ route('inventory.index') }}">Inventory</a>
                <i class="fa fa-angle-right mx-1"></i>
                Movements
            </small>
        </div>

        <div class="btn-group">
            <a href="{{ route('inventory.show', $inventory->id) }}"
               class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-arrow-left"></i> Back
            </a>

            <a href="{{ route('inventory.adjust', $inventory->id) }}"
               class="btn btn-sm btn-outline-warning">
                <i class="fa fa-sliders"></i> Adjust
            </a>
        </div>
    </div>

    {{-- Inventory Snapshot --}}
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card shadow-sm border-primary">
                <div class="card-body text-center">
                    <small class="text-muted">Material</small>
                    <div class="fw-bold">
                        {{ $inventory->material_name ?? $inventory->product?->our_part_no }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-info">
                <div class="card-body text-center">
                    <small class="text-muted">Item Type</small>
                    <div class="fw-bold text-uppercase">
                        {{ $inventory->inventory_type }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center">
                    <small class="text-muted">Available</small>
                    <h5 class="mb-0 text-success">
                        {{ $inventory->quantity_available }}
                    </h5>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body text-center">
                    <small class="text-muted">Reserved</small>
                    <h5 class="mb-0 text-warning">
                        {{ $inventory->quantity_reserved }}
                    </h5>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm mb-2">
        <div class="card-body py-2">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select id="typeFilter" class="form-select form-select-sm">
                        <option value="">All Movements</option>
                        <option value="purchase">Purchase</option>
                        <option value="grn">GRN</option>
                        <option value="sale">Sale</option>
                        <option value="issue">Issue</option>
                        <option value="adjustment">Adjustment</option>
                    </select>
                </div>

                <div class="col-md-4 ms-auto">
                    <input type="text"
                           id="searchInput"
                           class="form-control form-control-sm"
                           placeholder="Search reference / remarks / user">
                </div>
            </div>
        </div>
    </div>

    {{-- Movements Table --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="movementsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th class="text-end">Qty</th>
                            <th>Reference</th>
                            <th>Remarks</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movements as $move)
                        <tr data-type="{{ $move->movement_type }}">
                            <td>{{ $move->created_at->format('d M Y H:i') }}</td>

                            <td>
                                <span class="badge 
                                    @if(in_array($move->movement_type, ['purchase','grn','adjustment'])) bg-success
                                    @elseif(in_array($move->movement_type, ['sale','issue'])) bg-danger
                                    @else bg-secondary
                                    @endif
                                ">
                                    {{ strtoupper($move->movement_type) }}
                                </span>
                            </td>

                            <td class="text-end fw-bold
                                {{ $move->quantity < 0 ? 'text-danger' : 'text-success' }}">
                                {{ $move->quantity }}
                            </td>

                            <td>
                                @if($move->reference_type && $move->reference_id)
                                    {{ class_basename($move->reference_type) }}
                                    #{{ $move->reference_id }}
                                @else
                                    —
                                @endif
                            </td>

                            <td>{{ $move->remarks ?? '—' }}</td>

                            <td>{{ $move->user?->name ?? 'System' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($movements->isEmpty())
                    <div class="text-center text-muted py-4">
                        No inventory movements found.
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const typeFilter = document.getElementById('typeFilter');
    const searchInput = document.getElementById('searchInput');
    const rows = document.querySelectorAll('#movementsTable tbody tr');

    function filterTable() {
        const type = typeFilter.value.toLowerCase();
        const search = searchInput.value.toLowerCase();

        rows.forEach(row => {
            const rowType = row.dataset.type.toLowerCase();
            const text = row.innerText.toLowerCase();

            const typeMatch = !type || rowType === type;
            const searchMatch = !search || text.includes(search);

            row.style.display = (typeMatch && searchMatch) ? '' : 'none';
        });
    }

    typeFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('keyup', filterTable);
</script>
@endpush
