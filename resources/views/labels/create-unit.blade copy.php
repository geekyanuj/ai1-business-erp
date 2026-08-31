@extends('layouts.app')
@section('title', 'Unit Label Printing')

@section('content')

    <div class="mb-3">
        <h5 class="my-primary-color">Label Printing</h5>
        <small class="text-muted">
            <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
            <i class="fa-solid fa-angle-right"></i> Label Printing
        </small>
    </div>


    <div class="container">
        <div class="d-flex justify-content-end mt-3">
            <button class="btn btn-sm bg-my-primary text-white" data-bs-toggle="modal" data-bs-target="#unitLabelModal">
                <i class="fa fa-plus me-1"></i> Create Unit Product Label
            </button>
        </div>
        <div class="card mt-2">
            <div class="card-body">
                <table class="table table-bordered table-sm mb-0" id="LabelTable" data-url="{{ route('label.datatable') }}">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Lot No</th>
                            <th class="text-center">Client Name</th>
                            <th class="text-center">Notes</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="unitLabelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <!-- MODAL HEADER -->
            <div class="modal-header">
                <h5 class="modal-title my-primary-color">
                    <i class="fa fa-tag me-1"></i> Create Unit Product Label
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- MODAL BODY -->
            <div class="modal-body">

                <form method="POST" action="{{ route('labels.store') }}">
                    @csrf
                    <input type="hidden" name="label_type" value="unit">

                    <!-- Common Fields -->
                    <div class="row">
                        <div class="mb-3 col-md-4">
                            <label class="required-field">Lot No</label>
                            <input name="lot_no" class="form-control">
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="required-field">Client</label>
                            <select id="clientSelect" name="client_id" class="form-select" required>
                                <option value="">Select Client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 col-md-2">
                            <label class="required-field">Quantity</label>
                            <input type="number" class="form-control input-disabled-look" required>
                        </div>

                        <div class="mb-3">
                            <label class="">Notes</label>
                            <textarea name="notes" class="form-control"></textarea>
                        </div>
                    </div>

                    <hr>

                    <!-- LABELS -->
                    <div class="mb-3">
                        <label class="required-field">
                            Labels (one row = one sticker)
                        </label>

                        <div id="labels-container">

                            <!-- ONE LABEL ROW -->
                            <div class="label-row card shadow-sm mb-2">
                                <div class="card-body p-2">
                                    <div class="row g-2 align-items-end">

                                        <div class="col-md-4">
                                            <label class="small">TE Part No</label>
                                            <select class="form-select product-select"
                                                name="product_ids[]" required>
                                                <option value="">Select Product</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        data-part-no="{{ $product->our_part_no }}">
                                                        {{ $product->our_part_no }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="small">Client Part No</label>
                                            <input type="text"
                                                class="form-control client-part-no input-disabled-look"
                                                readonly>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="small">Item Code</label>
                                            <input type="text"
                                                name="item_codes[]"
                                                class="form-control item-input"
                                                required>
                                        </div>

                                        <div class="col-md-1 text-end">
                                            <div class="mb-1 fw-bold text-primary label-number">#1</div>
                                            <button type="button"
                                                class="btn btn-danger btn-sm"
                                                onclick="removeLabelRow(this)">
                                                ✕
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button"
                            class="btn btn-sm btn-secondary mt-2"
                            onclick="addLabelRow()">
                            + Add New Label
                        </button>
                    </div>

                    <hr>

                    <!-- MODAL FOOTER ACTIONS -->
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button class="btn btn-primary">
                            <i class="fa fa-save me-1"></i> Save
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
    <script>
        window.productsOptionsHtml = `{!! collect($products)->map(
        fn($p) =>
        "<option value='{$p->id}' data-part-no='{$p->our_part_no}'>
                                                    {$p->our_part_no}
                                                 </option>"
    )->implode('') !!}`;
    </script>
    @vite('resources/js/pages/unit-product-label-create.js')




@endpush