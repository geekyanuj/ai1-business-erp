@extends('layouts.app')

@section('title', isset($category) ? $category . ' Box Labels' : 'Box Label Printing')

@section('content')
<div class="container-fluid">

    {{-- ================= LOT SEARCH ================= --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h6 class="mb-2">
                <i class="fa fa-search me-1 my-primary-color"></i>
                Search {{ isset($category) ? $category . ' ' : '' }}Lot
            </h6>

            <div class="row">
                <div class="col-md-4 position-relative">
                    <input type="text"
                           id="lotSearch"
                           class="form-control"
                           placeholder="Type Lot No..."
                           autocomplete="off">

                    <div id="lotResults"
                         class="list-group position-absolute w-100 shadow"
                         style="z-index:1000; display:none;">
                    </div>
                </div>
            </div>

            <form id="lotSearchForm" method="GET" action="{{ route('labels.box.search') }}">
                <input type="hidden" name="lot_no" id="selectedLot">
                @if(isset($category))
                    <input type="hidden" name="category" value="{{ $category }}">
                @endif
            </form>
        </div>
    </div>

    {{-- ================= LOT DATA ================= --}}
    @isset($label)

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">
                <i class="fa fa-box-open my-primary-color me-1"></i>
                Lot Details
            </h5>
            <small class="text-muted">Box label configuration (Part No wise)</small>
        </div>
    </div>

    {{-- LOT SUMMARY --}}
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
                        <div class="text-muted small">Created</div>
                        <span class="badge bg-info text-dark">
                            {{ $label->created_at }}
                        </span>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 bg-light rounded">
                        <div class="text-muted small">Total Units</div>
                        <div class="fw-bold fs-5">
                            {{ $label->labelItems->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= BOX CONFIG FORM ================= --}}
    <form action="{{ route('labels.print-box', $label->id) }}"
          method="GET"
          target="_blank"
          id="boxLabelForm">
                @if(isset($category))
                        <input type="hidden" name="category" value="{{ $category }}">
                @endif

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fa fa-boxes-stacked me-1 my-primary-color"></i>
                    Box Label Configuration
                </h6>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th style="width:250px;">Our Part No</th>
                                <th>Description</th>
                                <th class="text-center">Total</th>
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
                                    <span class="small small-text">{{ $product->our_part_no }}</span>
                                    <input type="hidden"
                                           name="products[{{ $productId }}][product_id]"
                                           value="{{ $productId }}">
                                </td>

                                <td class="small text-muted">
                                    {{ $product->description }}
                                </td>

                                <td class="text-center">
                                    <input type="number"
                                           class="form-control form-control-sm text-center total-units"
                                           value="{{ $totalQty }}"
                                           readonly>
                                </td>

                                <td class="text-center">
                                    <input type="number"
                                           name="products[{{ $productId }}][units_per_box]"
                                           class="form-control form-control-sm text-center units-per-box"
                                           min="1" required>
                                    <small class="text-danger d-none units-error m-0 p-0" style="font-size:0.6rem">
                                        Units/box cannot exceed total
                                    </small>
                                </td>

                                <td class="text-center">
                                    <input type="number"
                                           class="form-control form-control-sm text-center box-count"
                                           readonly>
                                </td>

                                <td class="text-center">
                                    <input type="number"
                                           class="form-control form-control-sm text-center remainder"
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

    @endisset
</div>
@endsection

@push('scripts')
<script>
/* ================= LOT LIVE SEARCH ================= */
let debounce = null;

document.getElementById('lotSearch').addEventListener('input', function () {
    const q = this.value.trim();
    const results = document.getElementById('lotResults');

    clearTimeout(debounce);

    if (q.length < 2) {
        results.style.display = 'none';
        return;
    }

    debounce = setTimeout(() => {
        fetch(`{{ route('ajax.lots.search') }}?q=${encodeURIComponent(q)}{{ isset($category) ? '&category=' . urlencode($category) : '' }}`)
            .then(res => res.json())
            .then(data => {
                results.innerHTML = '';
                if (!data.length) {
                    results.innerHTML = `<div class="list-group-item small text-muted">No results</div>`;
                } else {
                    data.forEach(lot => {
                        results.innerHTML += `
                            <button type="button"
                                    class="list-group-item list-group-item-action"
                                    data-lot="${lot.lot_no}">
                                <strong>${lot.lot_no}</strong>
                                <div class="small text-muted">${lot.client.name} | ${lot.category ?? ''}</div>
                            </button>`;
                    });
                }
                results.style.display = 'block';
            });
    }, 300);
});

document.getElementById('lotResults').addEventListener('click', function (e) {
    const btn = e.target.closest('button');
    if (!btn) return;

    document.getElementById('selectedLot').value = btn.dataset.lot;
    document.getElementById('lotSearchForm').submit();
});

/* ================= BOX CALCULATION ================= */
function validateForm() {
    let error = false;

    document.querySelectorAll('.box-row').forEach(row => {
        const total = +row.querySelector('.total-units').value;
        const perBox = +row.querySelector('.units-per-box').value;
        const msg = row.querySelector('.units-error');

        if (!perBox || perBox > total) {
            error = true;
            row.classList.add('table-danger');
            msg.classList.remove('d-none');
        } else {
            row.classList.remove('table-danger');
            msg.classList.add('d-none');
        }
    });

    document.getElementById('printBoxBtn').disabled = error;
}

document.querySelectorAll('.units-per-box').forEach(input => {
    input.addEventListener('input', () => {
        const row = input.closest('.box-row');
        const total = +row.querySelector('.total-units').value;
        const perBox = +input.value;

        if (!perBox || perBox > total) return;

        const boxes = Math.floor(total / perBox);
        const rem = total % perBox;

        row.querySelector('.box-count').value = rem ? boxes + 1 : boxes;
        row.querySelector('.remainder').value = rem;

        validateForm();
    });
});
</script>
@endpush
