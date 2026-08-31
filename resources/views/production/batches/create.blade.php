@extends('layouts.app')
@section('title', 'Create Production Lot')

@section('content')

    {{-- Page Header --}}
    <div class="mb-3 d-flex justify-content-between">
        <div class="">
            <h5 class="my-primary-color">Create Production Lot</h5>
            <small class="text-muted">
                <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
                <i class="fa-solid fa-angle-right"></i>
                <a class="text-decoration-none" href="{{ route('production.batches.index') }}">Production Lots</a>
                <i class="fa-solid fa-angle-right"></i> Create
            </small>
        </div>
        {{-- Action Buttons --}}
            <div class="d-flex justify-content-end align-items-center gap-2 ">
                <a href="{{ route('production.batches.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>
    </div>



    <div class="row gx-0">
        <div class="col-md-12">

            {{-- Card --}}
            <div class="card shadow-sm">
                <div class="card-body">

                    {{-- Alerts --}}
                    <div class="alert alert-info py-2 mb-2">
                        Record the actual production lot, then start and complete it to add the finished quantity to inventory.
                    </div>

                    {{-- Validation Errors --}}
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Form --}}
                    <form method="POST" action="{{ route('production.batches.store') }}" class="container-fluid px-0"   >
                        @csrf

                        <div class="row gx-2">

                            {{-- Product --}}
                            <div class="col-md-6">
                                <label class="form-label required-field">Product</label>
                                <select name="product_id" class="form-select" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                            {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->our_part_no }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Batch No --}}
                            <div class="col-md-6">
                                <label class="form-label required-field">Lot Reference No</label>
                                <input type="text" name="batch_no" class="form-control"
                                    value="{{ old('batch_no') }}" required>
                            </div>

                            {{-- Lot No --}}
                            <div class="col-md-6">
                                <label class="form-label required-field">Lot No</label>
                                <input type="text" name="lot_no" class="form-control"
                                    value="{{ old('lot_no') }}">
                            </div>

                            {{-- Quantity --}}
                            <div class="col-md-6">
                                <label class="form-label required-field">Quantity Produced</label>
                                <input type="number" name="quantity_produced" class="form-control"
                                    min="1" value="{{ old('quantity_produced') }}" required>
                            </div>

                            {{-- Production Date --}}
                            <div class="col-md-6">
                                <label class="form-label required-field">Production Date</label>
                                <input type="date" name="production_date" class="form-control"
                                    value="{{ old('production_date') }}">
                            </div>

                            {{-- Expiry Date --}}
                            <div class="col-md-6">
                                <label class="form-label required-field">Expiry Date</label>
                                <input type="date" name="expiry_date" class="form-control"
                                    value="{{ old('expiry_date') }}">
                            </div>

                            {{-- Remarks --}}
                            <div class="col-md-12">
                                <label class="form-label">Remarks</label>
                                <textarea name="remarks" rows="3"
                                    class="form-control">{{ old('remarks') }}</textarea>
                            </div>

                        </div>

                        {{-- Submit --}}
                        <div class="text-end mt-3">
                                <button type="submit" class="btn bg-my-primary text-white btn-sm">
                                <i class="fa fa-save"></i> Create Lot
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection
