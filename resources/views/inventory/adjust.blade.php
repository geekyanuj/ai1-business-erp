@extends('layouts.app')

@section('title', 'Adjust Inventory')

@section('content')
<div class="container">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="my-primary-color mb-0">Inventory Adjustment</h5>
            <small class="text-muted">
                <a href="{{ route('inventory.index') }}">Inventory</a>
                <i class="fa fa-angle-right mx-1"></i>
                Adjust
            </small>
        </div>

        <div class="btn-group">
            <a href="{{ route('inventory.show', $inventory->id) }}"
               class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-arrow-left"></i> Back
            </a>

            <a href="{{ route('inventory.movements', $inventory->id) }}"
               class="btn btn-sm btn-outline-primary">
                <i class="fa fa-exchange-alt"></i> Movements
            </a>
        </div>
    </div>

    {{-- Inventory Snapshot --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card shadow-sm border-primary">
                <div class="card-body text-center">
                    <small class="text-muted">Available</small>
                    <h4 class="text-primary mb-0" id="availableQty">
                        {{ $inventory->quantity_available }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-warning">
                <div class="card-body text-center">
                    <small class="text-muted">Reserved</small>
                    <h4 class="text-warning mb-0">
                        {{ $inventory->quantity_reserved }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center">
                    <small class="text-muted">Free Stock</small>
                    <h4 class="text-success mb-0" id="freeStock">
                        {{ $inventory->quantity_available - $inventory->quantity_reserved }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Adjustment Form --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <strong>Adjustment Details</strong>
        </div>
        <div class="card-body">

            <table class="table table-sm mb-4">
                <tr>
                    <th width="30%">Material</th>
                    <td>{{ $inventory->material_name ?? $inventory->product?->our_part_no }}</td>
                </tr>
                <tr>
                    <th>Inventory Type</th>
                    <td>
                        <span class="badge bg-info text-dark">
                            {{ strtoupper($inventory->inventory_type) }}
                        </span>
                    </td>
                </tr>
            </table>

            <form method="POST" action="{{ route('inventory.adjust.store', $inventory->id) }}">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Adjustment Type</label>
                        <select name="adjustment_type"
                                id="adjustmentType"
                                class="form-select"
                                required>
                            <option value="add">➕ Add Stock</option>
                            <option value="remove">➖ Remove Stock</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Quantity</label>
                        <input type="number"
                               step="0.01"
                               name="quantity"
                               id="quantityInput"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Resulting Available</label>
                        <input type="text"
                               id="resultQty"
                               class="form-control"
                               readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Reason / Remarks</label>
                    <textarea name="remarks"
                              class="form-control"
                              rows="3"
                              placeholder="Damage, Audit correction, Opening balance..."
                              required></textarea>
                </div>

                <div class="alert alert-warning small">
                    <i class="fa fa-triangle-exclamation"></i>
                    Inventory adjustments are irreversible and logged permanently.
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('inventory.show', $inventory->id) }}"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>

                    <button type="submit"
                            class="btn btn-warning">
                        <i class="fa fa-check"></i> Apply Adjustment
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const availableQty = {{ $inventory->quantity_available }};
    const reservedQty = {{ $inventory->quantity_reserved }};

    const typeSelect = document.getElementById('adjustmentType');
    const qtyInput = document.getElementById('quantityInput');
    const resultInput = document.getElementById('resultQty');

    function calculateResult() {
        const qty = parseFloat(qtyInput.value || 0);
        const type = typeSelect.value;

        let result = availableQty;

        if (type === 'add') {
            result = availableQty + qty;
        } else {
            result = availableQty - qty;
        }

        resultInput.value = result >= 0 ? result : '⚠ Invalid';
    }

    typeSelect.addEventListener('change', calculateResult);
    qtyInput.addEventListener('input', calculateResult);
</script>
@endpush
