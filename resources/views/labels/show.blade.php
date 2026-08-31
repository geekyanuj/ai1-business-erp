@extends('layouts.app')

@section('title', 'Show Unit Label')

@section('content')
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-0">
                    <i class="fa fa-box-open my-primary-color me-1"></i>
                    Lot Details
                </h5>
                <small class="text-muted">View all items under this lot</small>
            </div>


            <div class="d-flex gap-2">
                <a href="{{ route('unit-label-create') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <!-- LOT SUMMARY -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">

                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small">Lot No</div>
                            <div class="fw-bold fs-5 my-primary-color">
                                {{ $label->lot_no }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small">Client</div>
                            <div class="fw-semibold">
                                {{ $label->client->name }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small">Category</div>
                            <div class="fw-semibold">{{ $label->category ?? $label->labelItems->first()?->product?->category ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small">Production Date & Time</div>
                            <span class="badge bg-info text-dark">
                                {{ ucfirst($label->created_at) }}
                            </span>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small">Total Items</div>
                            <div class="fw-bold fs-5">
                                {{ $label->labelItems->count() }}
                            </div>
                        </div>
                    </div>
                </div>

                @if($label->notes)
                    <div class="mt-3">
                        <div class="text-muted small">Notes</div>
                        <div class="fst-italic">
                            {{ $label->notes }}
                        </div>
                    </div>
                @endif

            </div>
        </div>

        <form action="{{ route('labels.print-box', $label->id) }}" method="GET" target="_blank" id="boxLabelForm">

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fa fa-boxes-stacked me-1 my-primary-color"></i>
                        Box Label Printing (Part No Wise)
                    </h6>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th style="width:300px;">Our Part No</th>
                                    <th>Description</th>
                                    <th class="text-center">Total Units</th>
                                    <th class="text-center">Units / Box</th>
                                    <th class="text-center">Boxes</th>
                                    <th class="text-center">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($label->labelItems->groupBy('product_id') as $productId => $items)
                                    @php
                                        $product = $items->first()->product;
                                        $totalQty = $items->count();
                                    @endphp

                                    <tr class="box-row">
                                        <td>
                                            <span class="small-text small">{{ $product->our_part_no }}</span>
                                            <input type="hidden" name="products[{{ $productId }}][product_id]"
                                                value="{{ $productId }}">
                                        </td>

                                        <td class="small text-muted">
                                            {{ $product->description }}
                                        </td>

                                        <td class="text-center">
                                            <input type="number" class="form-control form-control-sm text-center total-units"
                                                value="{{ $totalQty }}" readonly>
                                        </td>

                                        <td class="text-center">
                                            <input type="number" name="products[{{ $productId }}][units_per_box]"
                                                class="form-control form-control-sm text-center units-per-box" min="1" required>

                                            <small class="text-danger d-none units-error m-0 p-0" style="font-size:0.6rem">
                                                Units/box cannot exceed total
                                            </small>
                                        </td>

                                        <td class="text-center">
                                            <input type="number" class="form-control form-control-sm text-center box-count"
                                                readonly>
                                        </td>

                                        <td class="text-center">
                                            <input type="number" class="form-control form-control-sm text-center remainder"
                                                readonly>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-white text-end">
                    <button id="printBoxBtn" class="btn btn-primary" disabled>
                        <i class="fa fa-print me-1"></i>
                        Print Box Labels
                    </button>
                </div>
            </div>
        </form>



        <!-- ITEMS TABLE -->
        <div class="card shadow-sm border-1">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fa fa-list me-1 my-primary-color"></i>
                    Items in this Lot
                </h6>

                <span class="badge bg-secondary">
                    {{ $label->labelItems->count() }} Items
                </span>
            </div>

            <div class="table-responsive vh-100">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="table-light sticky-top small-text">
                        <tr>
                            <th>#</th>
                            <th>Our Part No</th>
                            <th>Item Code</th>
                            <th>{{ $label->client->name }} Part No</th>
                            <th class="text-center">Description</th>
                        </tr>
                    </thead>
                    <tbody class="overflow-y small-text">
                        @foreach ($label->labelItems as $index => $item)
                            @php
                                $mapping = $item->product->clientMappings
                                    ->where('client_id', $label->client_id)
                                    ->first();
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>

                                <td>
                                    <span class="text-primary">
                                        {{ $item->product->our_part_no }}
                                    </span>
                                </td>

                                <td>
                                    <code class="text-dark">
                                        {{ $item->item_code }}
                                    </code>
                                </td>

                                <td>
                                    @if($mapping)
                                        <code class="text-dark">
                                            {{ $mapping->client_part_no }}
                                        </code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    <code class="text-dark fst-italic">
                                        {{ $item->product->description }}
                                    </code>
                                </td>

                                
                            </tr>
                        @endforeach

                        @if($label->labelItems->isEmpty())
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No items found for this lot
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
@push('scripts')

    <!-- @vite('resources/js/pages/unit-product-label-create.js') -->


    <script>
        function validateBoxForm() {
            let hasError = false;

            document.querySelectorAll(".box-row").forEach(row => {
                const total = parseInt(row.querySelector(".total-units").value || 0);
                const perBox = parseInt(row.querySelector(".units-per-box").value || 0);

                const error = row.querySelector(".units-error");

                if (!perBox || perBox > total) {
                    hasError = true;
                    row.classList.add("table-danger");
                    error.classList.remove("d-none");
                } else {
                    row.classList.remove("table-danger");
                    error.classList.add("d-none");
                }
            });

            document.getElementById("printBoxBtn").disabled = hasError;
        }

        function calculateBoxes(row) {
            const total = parseInt(row.querySelector(".total-units").value || 0);
            const perBox = parseInt(row.querySelector(".units-per-box").value || 0);

            if (!perBox || perBox > total) return;

            const boxes = Math.floor(total / perBox);
            const remainder = total % perBox;

            row.querySelector(".box-count").value = remainder ? boxes + 1 : boxes;
            row.querySelector(".remainder").value = remainder;

            validateBoxForm();
        }

        document.querySelectorAll(".units-per-box").forEach(input => {
            input.addEventListener("input", () => {
                calculateBoxes(input.closest(".box-row"));
            });
        });
    </script>


@endpush