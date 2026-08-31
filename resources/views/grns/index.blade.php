@extends('layouts.app')

@section('title', 'GRNs')

@section('content')
<div class="container mt-2">

<div>
    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="fw-bold my-primary-color">
            <i class="fa fa-box me-2"></i> Goods Receipt Notes
        </h5>

        <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    {{-- ================= BREADCRUMB ================= --}}
    <div class="mb-3">
        <small class="text-muted">
            <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
            <i class="fa fa-angle-right"></i>
            <a class="text-decoration-none" href="{{ route('purchase-orders.index') }}">Purchase Orders</a>
            <i class="fa fa-angle-right"></i>
            <span>GRNs</span>
        </small>
    </div>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover align-middle" id="grnsTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>GRN No</th>
                        <th>PO No</th>
                        <th>Received Date</th>
                        <th>Received By</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($grns as $grn)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $grn->grn_number }}</td>
                            <td>{{ $grn->purchaseOrder->po_number }}</td>
                            <td>{{ $grn->received_date }}</td>
                            <td>{{ $grn->receiver->name ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('grns.show', $grn->id) }}"
                                   class="btn btn-outline-info btn-sm">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection

@push('scripts')
    @vite('resources/js/pages/purchase-order/grns/grns-index.js')
@endpush
