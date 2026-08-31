@extends('layouts.app')
@section('title', 'Create BOM')

@section('content')

    {{-- Page Header --}}
    <div class="mb-3 d-flex justify-content-between">
        <div class="">
            <h5 class="my-primary-color">Create BOM</h5>
            <small class="text-muted">
                <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
                <i class="fa-solid fa-angle-right"></i>
                <a class="text-decoration-none" href="{{ route('production.boms.index') }}">BOM Master</a>
                <i class="fa-solid fa-angle-right"></i> Create
            </small>
        </div>
        {{-- Action Buttons --}}
        <div class="d-flex justify-content-end align-items-center gap-2 ">
            <a href="{{ route('production.boms.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </div>


    <div class="row gx-0">
        <div class="col-md-12">

            {{-- Card --}}
            <div class="card shadow-sm position-relative {{ $inventories->count() == 0 ? 'opacity-50' : '' }}">
                @if ($inventories->count() == 0)
                    <div class="position-absolute top-0 start-0 w-100 h-100 
                                            d-flex align-items-center justify-content-center
                                            bg-white bg-opacity-75 z-3 rounded">
                        <div class="text-center">
                            <i class="fas fa-box-open fa-2x text-danger mb-2"></i>
                            <h6 class="text-danger mb-1">No data in inventory</h6>
                            <p class="mb-0 text-muted">BOM can’t be created</p>
                        </div>
                    </div>
                @endif

                <div class="card-body">

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
                    <form method="POST" action="{{ route('production.boms.store') }}" 
                          class="container-fluid px-0" 
                          {{ $inventories->count() == 0 ? 'onsubmit=return false' : '' }}>
                        @csrf

                        <div class="row gx-2 mb-3">
                            {{-- Product --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label required-field fw-bold">Product (to manufacture)</label>
                                <select name="product_id" class="form-select" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->our_part_no }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-bold">Remarks</label>
                                <input type="text" name="remarks" class="form-control" value="{{ old('remarks') }}">
                            </div>
                        </div>

                        <hr>

                        {{-- BOM Items --}}
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">BOM Items (Materials Needed)</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-row">
                                <i class="fa fa-plus"></i> Add Material
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="bom-items" 
                                   data-inventories='@json($inventories->values())'
                                   data-disabled="{{ $inventories->count() == 0 ? 'true' : 'false' }}">
                                <thead class="table-light">
                                    <tr>
                                        <th>Material</th>
                                        <th width="200">Qty / Unit</th>
                                        <th width="80" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="items[0][material_name]" class="form-select material-select" required>
                                                <option value="">-- Select Raw Material --</option>
                                                @foreach($inventories as $inventory)
                                                    <option value="{{ $inventory->material_name }}" data-uom="{{ $inventory->uom }}">
                                                        {{ $inventory->material_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="number" step="0.001" min="0.001" name="items[0][quantity_per_unit]"
                                                    class="form-control text-end" required>
                                                <span class="uom-text"></span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="fa fa-times"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Submit --}}
                        <div class="text-end mt-4">
                            <button type="submit" class="btn bg-my-primary text-white btn-sm px-4">
                                <i class="fa fa-save"></i> Save BOM
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @vite('resources/js/pages/production/bom-create.js')
@endpush