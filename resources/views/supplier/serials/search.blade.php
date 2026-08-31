@extends('layouts.app')

@section('title', 'Serial Number Search')

@section('content')
<div class="container">

    <h5 class="fw-bold mb-3">
        <i class="fas fa-search me-2"></i> Serial Number Lookup
    </h5>

    {{-- SEARCH FORM --}}
    <form method="GET" action="{{ route('supplier.serial.search') }}" class="mb-4">
        <div class="input-group">
            <input type="text"
                   name="serial"
                   value="{{ $serial ?? '' }}"
                   class="form-control"
                   placeholder="Enter Supplier Serial Number"
                   required>

            <button class="btn btn-primary">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
    </form>

    {{-- RESULT --}}
    @if(isset($serial))
        @if($result)
            <div class="card shadow-sm">
                <div class="card-body">

                    <h6 class="fw-bold text-success mb-3">
                        <i class="fas fa-check-circle me-1"></i>
                        Serial Found
                    </h6>

                    <table class="table table-bordered mb-0">
                        <tr>
                            <th width="30%">Supplier Serial</th>
                            <td>{{ $result->supplier_serial }}</td>
                        </tr>

                        <tr>
                            <th>Product</th>
                            <td>
                                {{ $result->grnItem->product->our_part_no ?? $result->grnItem->material_name }}
                            </td>
                        </tr>

                        <tr>
                            <th>GRN Number</th>
                            <td>
                                <a href="{{ route('grns.show', $result->grnItem->grn->id) }}">
                                    {{ $result->grnItem->grn->grn_number }}
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <th>Purchase Order</th>
                            <td>
                                <a href="{{ route('purchase-orders.show', $result->grnItem->grn->purchaseOrder->id) }}">
                                    {{ $result->grnItem->grn->purchaseOrder->po_number }}
                                </a>
                            </td>
                        </tr>

                        <tr>
                            <th>Received Date</th>
                            <td>{{ $result->grnItem->grn->received_date }}</td>
                        </tr>
                    </table>

                </div>
            </div>
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-1"></i>
                No GRN found for serial number <strong>{{ $serial }}</strong>
            </div>
        @endif
    @endif

</div>
@endsection
