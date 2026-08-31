@extends('layouts.app')
@section('title')
    Products
@endsection

@section('content')
    <div class="mb-3">
        <h5 class="my-primary-color">Products</h5>
        <small class="text-muted">
            <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
            <i class="fa-solid fa-angle-right"></i> Products
        </small>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="user-action">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <!-- Category Filter -->
                        <select id="categoryFilter" class="form-select form-select-sm" style="width: 200px;">
                            <option value="">All Categories</option>
                            <option value="RF Antenna">RF Antenna</option>
                            <option value="RF Cable Assembly">RF Cable Assembly</option>
                            <option value="RF Cable">RF Cable</option>
                            <option value="Microwave Devices">Microwave Devices</option>
                            <option value="IoT">IoT</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-sm text-white bg-my-primary" data-bs-toggle="modal"
                            data-bs-target="#addProductModal">
                            <i class="fa-solid fa-plus"></i> Add New Product
                        </button>
                        <button id="exportExcelBtn" class="btn btn-success btn-sm">
                            <i class="fa-solid fa-file-excel"></i> Export Excel
                        </button>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body my-0 py-0">
                        <table id="productsTable" data-url="{{ route('products.data') }}"
                            class="table table-sm table-bordered table-striped pt-2 w-100">
                            <thead>
                                <tr>
                                    <!-- <th>ID</th> -->
                                    <th>Our Part No</th>
                                    {{-- <th>Description</th> --}}
                                    <th>Category</th>
                                    <th>Specs</th>
                                    <th>HSN</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="addProductForm" action="{{ route('products.store') }}" method="POST">
                    @csrf
                    <div class="modal-header py-2">
                        <h6 class="modal-title my-primary-color" id="addProductModalLabel">Add New Product</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="our_part_no" class="form-label mb-1 required-field">Our Part No</label>
                                <input type="text" name="our_part_no" id="our_part_no" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label for="category" class="form-label mb-1 required-field">Category</label>
                                <select name="category" id="category" class="form-select" required>
                                    <option value="RF Antenna" selected>RF Antenna</option>
                                    <option value="RF Cable Assembly">RF Cable Assembly</option>
                                    <option value="RF Cable">RF Cable</option>
                                    <option value="Microwave Devices">Microwave Devices</option>
                                    <option value="IoT">IoT</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="hsn" class="form-label mb-1">HSN</label>
                                <input type="text" name="hsn" id="hsn" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label for="description" class="form-label mb-1">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label for="specs" class="form-label mb-1">Specifications</label>
                                <textarea name="specs" id="specs" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm px-3">
                            <i class="fas fa-save me-1"></i> Save Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editProductForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header py-2">
                        <h5 class="modal-title my-primary-color" id="editProductModalLabel">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="id" id="editProductId">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="editProductPartNo" class="form-label mb-1 required-field">Our Part No</label>
                                <input type="text" name="our_part_no" id="editProductPartNo" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label for="editProductCategory" class="form-label mb-1 required-field">Category</label>
                                <select name="category" id="editProductCategory" class="form-select" required>
                                    <option value="RF Antenna" selected>RF Antenna</option>
                                    <option value="RF Cable Assembly">RF Cable Assembly</option>
                                    <option value="RF Cable">RF Cable</option>
                                    <option value="Microwave Devices">Microwave Devices</option>
                                    <option value="IoT">IoT</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="hsn" class="form-label mb-1">HSN</label>
                                <input type="text" name="hsn" class="form-control" id="editProductHsn">
                            </div>
                            <div class="col-md-12">
                                <label for="editProductDescription" class="form-label mb-1">Description</label>
                                <textarea name="description" id="editProductDescription" class="form-control"
                                    rows="3"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label for="editProductSpecs" class="form-label mb-1">Specifications</label>
                                <textarea name="specs" id="editProductSpecs" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm px-3">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/products-index.js')
@endpush