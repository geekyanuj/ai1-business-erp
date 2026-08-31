@extends('layouts.app')
@section('title', 'Production Lot Details')

@section('title', 'Production Lot Details')

@section('content')
    <div class="container">

        {{-- ===== HEADER ===== --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>

                <h5 class="fw-bold my-primary-color">
                    <i class="fas fa-industry me-2"></i>
                    Production Lot: {{ $batch->lot_no ?: $batch->batch_no }}
                </h5>
                <small class="text-muted">
                    <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
                    <i class="fa-solid fa-angle-right"></i><a class="text-decoration-none" href="{{ route('production.batches.index') }}">Production</a>
                    <i class="fa-solid fa-angle-right"></i>  Production Lot
                </small>
            </div>

            <a href="{{ route('production.batches.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        {{-- ===== STATUS ===== --}}
        <div class="mb-3">
            <span class="badge
                        @if($batch->status === 'draft') bg-warning
                        @elseif($batch->status === 'in_progress') bg-warning
                        @elseif($batch->status === 'completed') bg-success
                        @else bg-secondary
                        @endif
                    ">
                {{ strtoupper($batch->status) }}
            </span>
        </div>

        {{-- ===== DETAILS CARD ===== --}}
        <div class="card shadow-sm mb-3">
            <div class="card-body">

                <div class="row mb-2">
                    <div class="col-md-4">
                        <strong>Product</strong><br>
                        {{ $batch->product->our_part_no }}
                    </div>

                    <div class="col-md-4">
                        <strong>Quantity Produced</strong><br>
                        {{ $batch->quantity_produced }}
                    </div>

                    <div class="col-md-4">
                        <strong>Operator</strong><br>
                        {{ $batch->operator->name ?? '-' }}
                    </div>
                </div>

                <hr>

                <div class="row mb-2">
                    <div class="col-md-4">
                        <strong>Production Date</strong><br>
                        {{ $batch->production_date ?? '-' }}
                    </div>

                    <div class="col-md-4">
                        <strong>Expiry Date</strong><br>
                        {{ $batch->expiry_date ?? '-' }}
                    </div>

                    <div class="col-md-4">
                        <strong>Lot No</strong><br>
                        {{ $batch->lot_no ?? '-' }}
                    </div>
                </div>

                @if($batch->remarks)
                    <hr>
                    <strong>Remarks</strong>
                    <p class="mb-0">{{ $batch->remarks }}</p>
                @endif

            </div>
        </div>

        {{-- ===== PRODUCTION STATUS ===== --}}
        <div class="card shadow-sm mb-3">
            <div class="card-header fw-bold">
                <i class="fas fa-clipboard-check me-1"></i> Production Flow
            </div>
            <div class="card-body p-0">
                <p class="mb-0">Start the batch when work begins. Complete it when the finished quantity is ready; completion updates ready inventory with {{ $batch->quantity_produced }} pcs.</p>
            </div>
        </div>


        {{-- ===== ACTIONS ===== --}}
        <div class="mt-4 mb-4">
            @if($batch->status === 'draft')
                <form method="POST" action="{{ route('production.batches.start', $batch->id) }}">
                    @csrf
                    <button class="btn btn-warning">
                        <i class="fas fa-play"></i> Start Production
                    </button>
                </form>
            @endif

            @if($batch->status === 'in_progress')
                <form method="POST" action="{{ route('production.batches.complete', $batch->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Complete production and update inventory?')">
                    <i class="fas fa-check-circle"></i> Complete Production
                    </button>
                </form>
            @endif
        </div>




    </div>
@endsection