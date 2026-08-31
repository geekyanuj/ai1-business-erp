@extends('layouts.app')

@section('title')
    Product Details
@endsection

@section('content')
    <div class="container mt-2">


        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center ">
            <h5 class="fw-bold my-primary-color">
                <i class="fas fa-box me-2"></i> {{ $product->our_part_no }}
            </h5>


            <div>
                <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>

                <button class="btn btn-sm btn-primary edit-product-btn" data-product='@json($product)' data-id="{{ $product->id }}" data-bs-toggle="modal"
                    data-bs-target="#editProductModal" >
                    <i class="fas fa-edit"></i> Edit
                </button>

            </div>
        </div>
        <div class="mb-3">
            <small class="text-muted">
                <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
                <i class="fa-solid fa-angle-right"></i>
                <a class="text-decoration-none" href="{{ route('products.index') }}">Products</a>
                <i class="fa-solid fa-angle-right"></i> {{ $product->category }}
                </small>
        </div>


        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-3" id="productTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab-details">
                    <i class="fas fa-info-circle"></i> Details
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-inventory">
                    <i class="fas fa-layer-group"></i> Inventory
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-purchase">
                    <i class="fas fa-shopping-cart"></i> Purchases
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-sales">
                    <i class="fas fa-file-invoice"></i> Sales
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-logs">
                    <i class="fas fa-history"></i> Activity Log
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-qr">
                    <i class="fas fa-qrcode"></i> QR Code
                </a>
            </li>
        </ul>


        <div class="tab-content">

            {{-- DETAILS TAB --}}
            <div class="tab-pane fade show active" id="tab-details">
                <div class="card shadow-sm">
                    <div class="card-body">

                        <h5 class="fw-bold text-secondary">Basic Information</h5>
                        <hr>

                        <p><strong>Part No:</strong> {{ $product->our_part_no }}</p>
                        <p><strong>Category:</strong>
                            <span class="badge bg-info px-3">{{ $product->category }}</span>
                        </p>
                        <p><strong>Description:</strong> {{ $product->description }}</p>
                        <p><strong>HSN Code:</strong> {{ $product->hsn }}</p>

                        <h6 class="fw-bold text-secondary mt-4">Specifications</h6>
                        <div class="border rounded p-2 bg-light">
                            @if($product->specs)
                                <pre class="m-0">{{ $product->specs }}</pre>
                            @else
                                <span class="text-muted">No specs added.</span>
                            @endif
                        </div>

                    </div>
                </div>
            </div>



            {{-- INVENTORY TAB --}}
            <div class="tab-pane fade" id="tab-inventory">
                <div class="card shadow-sm">
                    <div class="card-body">

                        <h5 class="fw-bold text-secondary">Inventory Overview</h5>
                        <hr>

                        @if($inventories->count())
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Location</th>
                                        <th>Lot No</th>
                                        <th>Qty Available</th>
                                        <th>Qty Reserved</th>
                                        <th>Serial Numbers</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventories as $inv)
                                        <tr>
                                            <td>{{ $inv->location }}</td>
                                            <td>{{ $inv->lot_no }}</td>
                                            <td>{{ $inv->quantity_available }}</td>
                                            <td>{{ $inv->quantity_reserved }}</td>
                                            <td>
                                                @if($inv->serial_numbers)
                                                    <pre>{{ json_encode($inv->serial_numbers, JSON_PRETTY_PRINT) }}</pre>
                                                @else
                                                    <span class="text-muted">No serials</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted">No inventory found for this product.</p>
                        @endif

                    </div>
                </div>
            </div>



            {{-- PURCHASE TAB --}}
            <div class="tab-pane fade" id="tab-purchase">
                <div class="card shadow-sm">
                    <div class="card-body">

                        <h5 class="fw-bold text-secondary">Purchase History</h5>
                        <hr>

                        @if($purchaseItems->count())
                            <table class="table table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>PO Number</th>
                                        <th>Qty</th>
                                        <th>Unit Price</th>
                                        <th>Order Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseItems as $item)
                                        <tr>
                                            <td>{{ $item->purchaseOrder->po_number }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->unit_price }}</td>
                                            <td>{{ $item->purchaseOrder->ordered_date }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted">No purchase orders found.</p>
                        @endif

                    </div>
                </div>
            </div>



            {{-- SALES TAB --}}
            <div class="tab-pane fade" id="tab-sales">
                <div class="card shadow-sm">
                    <div class="card-body">

                        <h5 class="fw-bold text-secondary">Sales History</h5>
                        <hr>

                        @if($salesItems->count())
                            <table class="table table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Invoice Number</th>
                                        <th>Invoice Date</th>
                                        <th>Client</th>
                                        <th>Qty Ordered</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salesItems as $item)
                                        <tr>
                                            <td>{{ $item->salesInvoice->invoice_number }}</td>
                                            <td>{{ $item->salesInvoice->invoice_date }}</td>
                                            <td>{{ $item->salesInvoice->client->name }}</td>
                                            <td>{{ $item->quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted">No sales orders found.</p>
                        @endif

                    </div>
                </div>
            </div>



            {{-- ACTIVITY LOG TAB --}}
            <div class="tab-pane fade" id="tab-logs">
                <div class="card shadow-sm">
                    <div class="card-body">

                        <h5 class="fw-bold text-secondary">Activity Log</h5>
                        <hr>

                        @if($activityLogs->count())
                            <ul class="list-group">
                                @foreach($activityLogs as $log)
                                    <li class="list-group-item">
                                        <strong>{{ $log->event }}</strong> —
                                        {{ $log->description }}
                                        <br>
                                        <small class="text-muted">
                                            {{ $log->created_at->format('d M Y, h:i A') }} |
                                            By: {{ optional($log->causer)->name ?? 'System' }}
                                        </small>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">No activity recorded.</p>
                        @endif

                    </div>
                </div>
            </div>


            {{-- QR CODE TAB --}}
            <div class="tab-pane fade" id="tab-qr">
                <div class="card shadow-sm">
                    <div class="card-body text-center">

                        <h5 class="fw-bold text-secondary">Product QR Code</h5>
                        <hr>


                        <img src="{{ $qrSvg }}" class="img-fluid" style="max-width:200px;">

                        <p class="text-muted mt-2">QR encodes: <strong>{{ $product->our_part_no }}</strong></p>
                        <p class="mt-3">
                            <strong>Scan to identify product</strong>
                        </p>

                    </div>
                </div>
            </div>

        </div>
    </div>


    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form id="editProductForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-header py-2">
                        <h5 class="modal-title my-primary-color" id="editProductModalLabel">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="id" id="editProductId">
                        <div class="row g-2">
                            <div class="form-group col-md-4">
                                <label class="required-field" for="editProductPartNo">Our Part No</label>
                                <input type="text" name="our_part_no" id="editProductPartNo" class="form-control">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="editProductCategory" class="required-field">Product Category</label>
                                <select class="form-select" name="category" id="editProductCategory" required>
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

                            <div class="form-group col-md-12">
                                <label for="editProductDescription">Description</label>
                                <textarea class="form-control" id="editProductDescription" name="description"
                                    rows="3"></textarea>
                            </div>

                            

                            <div class="form-group col-md-12">
                                <label for="editProductSpecs">Specification</label>
                                <textarea class="form-control" id="editProductSpecs" name="specs" rows="3"></textarea>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')

    @vite('resources/js/pages/products-show.js')

@endpush