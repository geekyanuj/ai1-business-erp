@extends('layouts.app')

@section('title', 'Add Inventory Item')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="my-primary-color"><i class="fa fa-plus-circle me-2"></i>Add Inventory Item</h5>
            <small class="text-muted">
                <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
                <i class="fa-solid fa-angle-right"></i>
                <a class="text-decoration-none" href="{{ route('inventory.index') }}">Inventory</a>
                <i class="fa-solid fa-angle-right"></i> Add Item
            </small>
        </div>
        <a href="{{ route('inventory.index') }}" class="btn btn-sm btn-secondary">
            <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">
                    <i class="fa fa-boxes-stacked me-1"></i> New Inventory Item
                </div>
                <div class="card-body">
                    <form action="{{ route('inventory.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="inventory_type" id="inventoryType" class="form-select @error('inventory_type') is-invalid @enderror" required>
                                <option value="">-- Select Category --</option>
                                <option value="raw" {{ old('inventory_type') === 'raw' ? 'selected' : '' }}>
                                    Raw Material
                                </option>
                                <option value="equipment" {{ old('inventory_type') === 'equipment' ? 'selected' : '' }}>
                                    Equipment &amp; Tool
                                </option>
                            </select>
                            @error('inventory_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Note: Ready Products are added automatically via Production Lots.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Item Name / Description <span class="text-danger">*</span></label>
                            <input type="text" name="material_name" class="form-control @error('material_name') is-invalid @enderror"
                                value="{{ old('material_name') }}" placeholder="e.g. Steel Rod 10mm / Digital Multimeter" required>
                            @error('material_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Unit of Measure (UOM) <span class="text-danger">*</span></label>
                                <select name="uom" class="form-select @error('uom') is-invalid @enderror" required>
                                    <option value="pcs" {{ old('uom','pcs') === 'pcs' ? 'selected' : '' }}>pcs</option>
                                    <option value="kg" {{ old('uom') === 'kg' ? 'selected' : '' }}>kg</option>
                                    <option value="meter" {{ old('uom') === 'meter' ? 'selected' : '' }}>meter</option>
                                    <option value="liter" {{ old('uom') === 'liter' ? 'selected' : '' }}>liter</option>
                                    <option value="set" {{ old('uom') === 'set' ? 'selected' : '' }}>set</option>
                                    <option value="no" {{ old('uom') === 'no' ? 'selected' : '' }}>no.</option>
                                </select>
                                @error('uom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Opening Quantity</label>
                                <input type="number" step="0.01" name="quantity_available"
                                    class="form-control @error('quantity_available') is-invalid @enderror"
                                    value="{{ old('quantity_available', 0) }}" min="0">
                                @error('quantity_available')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Storage Location</label>
                            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                                value="{{ old('location') }}" placeholder="e.g. Main Warehouse, Store Room A">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="descriptionGroup">
                            <label class="form-label fw-semibold">Additional Details / Notes</label>
                            <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                                placeholder="Equipment model, serial number, condition, etc.">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save me-1"></i> Save Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
