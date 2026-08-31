@extends('layouts.app')

@section('title', 'Serial Traceability')

@section('content')
    <div class="mb-3">
        <h5 class="my-primary-color">Serial Traceability</h5>
        <small class="text-muted">
            <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
            <i class="fa-solid fa-angle-right"></i> Serial Traceability
        </small>
    </div>

    <div class="container-fluid">
        <form method="GET" action="{{ route('labels.traceability') }}" class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label for="traceCategory">Category</label>
                        <select id="traceCategory" name="category" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="traceProduct">Part No</label>
                        <select id="traceProduct" name="product_id" class="form-select form-select-sm">
                            <option value="">All Part Numbers</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-category="{{ $product->category }}" @selected((string) request('product_id') === (string) $product->id)>
                                    {{ $product->our_part_no }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="traceClient">Client</label>
                        <select id="traceClient" name="client_id" class="form-select form-select-sm">
                            <option value="">All Clients</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" @selected((string) request('client_id') === (string) $client->id)>{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="traceSerial">Serial / Item Code</label>
                        <input id="traceSerial" name="serial" value="{{ request('serial') }}" class="form-control form-control-sm" placeholder="Search serial">
                    </div>
                    <div class="col-md-2">
                        <label for="traceLot">Lot No</label>
                        <input id="traceLot" name="lot_no" value="{{ request('lot_no') }}" class="form-control form-control-sm" placeholder="Search lot">
                    </div>
                    <div class="col-md-1 d-flex gap-1">
                        <button class="btn btn-sm btn-primary" title="Search"><i class="fa fa-search"></i></button>
                        <a href="{{ route('labels.traceability') }}" class="btn btn-sm btn-outline-secondary" title="Clear"><i class="fa fa-rotate-left"></i></a>
                    </div>
                </div>
            </div>
        </form>

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fa fa-list me-1 my-primary-color"></i> Product serial history</h6>
                <span class="badge bg-secondary">{{ $items->total() }} results</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Category</th>
                            <th>Part No</th>
                            <th>Serial No</th>
                            <th>Item Code</th>
                            <th>Lot No</th>
                            <th>Production Lot</th>
                            <th>Sent To Client</th>
                            <th>Printed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td>{{ $item->product->category }}</td>
                                <td>{{ $item->product->our_part_no }}</td>
                                <td>{{ $item->serial_no }}</td>
                                <td><code>{{ $item->item_code }}</code></td>
                                <td>{{ $item->label->lot_no }}</td>
                                <td>{{ $item->label->productionBatch->batch_no ?? '-' }}</td>
                                <td>{{ $item->label->client->name ?? '-' }}</td>
                                <td>{{ $item->created_at?->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No serial records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($items->hasPages())
                <div class="card-footer bg-white">{{ $items->links() }}</div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const category = document.getElementById('traceCategory');
    const product = document.getElementById('traceProduct');
    const allProducts = [...product.options];
    category.addEventListener('change', function () {
        const selected = product.value;
        product.innerHTML = allProducts
            .filter(option => !option.value || option.dataset.category === this.value)
            .map(option => option.outerHTML)
            .join('');
        product.value = allProducts.some(option => option.value === selected && option.dataset.category === this.value) ? selected : '';
    });
</script>
@endpush
