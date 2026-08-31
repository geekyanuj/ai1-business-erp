@extends('layouts.app')

@section('title', 'GRN Details')

@section('content')
<div class="container">

{{-- ================= HEADER ================= --}}
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold my-primary-color">
                <i class="fas fa-file-invoice me-2"></i>
                {{ $grn->grn_number }}
            </h5>

            <div class="d-flex gap-2">
                <a href="{{ route('purchase-orders.show', $grn->purchaseOrder->id) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        {{-- ================= BREADCRUMB ================= --}}
        <div class="mb-3">
            <small class="text-muted">
                <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
                <i class="fa fa-angle-right"></i>
                <a class="text-decoration-none" href="{{ route('purchase-orders.index') }}">Purchase Orders</a>
                <i class="fa fa-angle-right"></i>
                <a class="text-decoration-none" href="{{ route('purchase-orders.show', $grn->purchaseOrder->id) }}">{{ $grn->purchaseOrder->po_number }}</a>
                <i class="fa fa-angle-right"></i>
                {{ $grn->grn_number }}
            </small>
        </div>


    <div class="card shadow-sm mb-3">
        <div class="card-body">

            <div class="row mb-2">
                <div class="col-md-4">
                    <strong>Purchase Order:</strong><br>
                    {{ $grn->purchaseOrder->po_number }}
                </div>

                <div class="col-md-4">
                    <strong>Received Date:</strong><br>
                    {{ $grn->received_date }}
                </div>

                <div class="col-md-4">
                    <strong>Received By:</strong><br>
                    {{ $grn->receiver->name ?? '-' }}
                </div>
            </div>

            <hr>

            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Quantity Received</th>
                        @if ($grn->purchaseOrder->po_type === 'ready')
                            <th>Serial Numbers</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @foreach($grn->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>
                            {{ $item->product->our_part_no ?? $item->material_name }}
                        </td>

                        <td>{{ $item->quantity_received }}</td>

                        @if ($grn->purchaseOrder->po_type === 'ready')
                            <td>
                                @if($item->serials->count())
                                    <a class="btn btn-sm btn-outline-info" data-bs-toggle="collapse" href="#serials-{{ $item->id }}">
                                        View Serials ({{ $item->serials->count() }})
                                    </a>

                                    <div class="collapse mt-2" id="serials-{{ $item->id }}">
                                        @foreach($item->serials as $serial)
                                            <span class="badge bg-secondary">{{ $serial->supplier_serial }}</span>
                                        @endforeach
                                    </div>

                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>

            </table>

            @if($grn->remarks)
                <div class="mt-3">
                    <strong>Remarks:</strong>
                    <p>{{ $grn->remarks }}</p>
                </div>
            @endif

        </div>
    </div>

</div>
@endsection
