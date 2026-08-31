@extends('layouts.app')

@section('title', $category . ' Labels')

@section('content')
    <div class="mb-3">
        <h5 class="my-primary-color">{{ $category }} Labels</h5>
        <small class="text-muted">
            <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
            <i class="fa-solid fa-angle-right"></i> {{ $category }} Labels
        </small>
    </div>

    <div class="container">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fa fa-industry me-1 my-primary-color"></i> Create labels from production</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('labels.category.store', ['category' => $category]) }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="productionBatch" class="required-field">Production Lot</label>
                            <select id="productionBatch" name="production_batch_id" class="form-select" required>
                                <option value="">Select production lot</option>
                                @foreach ($batches as $batch)
                                    <option value="{{ $batch->id }}" data-product="{{ $batch->product_id }}" data-quantity="{{ $batch->quantity_produced }}">
                                        {{ $batch->batch_no }} - {{ $batch->product->our_part_no }} ({{ $batch->quantity_produced }} pcs)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="productId" class="required-field">Part No</label>
                            <select id="productId" name="product_id" class="form-select" required disabled>
                                <option value="">Select production lot first</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->our_part_no }} - {{ $product->description }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="clientId">Client <span class="text-muted">(Optional)</span></label>
                            <select id="clientId" name="client_id" class="form-select">
                                <option value="">Select client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="quantity" class="required-field">Label Quantity</label>
                            <input id="quantity" name="quantity" type="number" class="form-control" min="1" required disabled>
                            <small id="quantityHelp" class="text-muted">Choose a production lot first.</small>
                        </div>

                        <div class="col-md-4">
                            <label for="prefix">Serial Prefix</label>
                            <input id="prefix" name="prefix" type="text" class="form-control" placeholder="Automatic from part and batch">
                        </div>

                        <div class="col-md-12">
                            <label for="lotNo">Lot No</label>
                            <input id="lotNo" name="lot_no" class="form-control" placeholder="Uses the production lot when blank">
                        </div>

                        <div class="col-md-12">
                            <label for="notes">Notes</label>
                            <textarea id="notes" name="notes" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button class="btn bg-my-primary text-white" type="submit"><i class="fa fa-save me-1"></i> Create Labels</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('productionBatch').addEventListener('change', function () {
            const option = this.options[this.selectedIndex];
            const product = document.getElementById('productId');
            const quantity = document.getElementById('quantity');
            const help = document.getElementById('quantityHelp');
            const productId = option.dataset.product || '';
            const maxQuantity = option.dataset.quantity || '';

            product.disabled = !productId;
            product.value = productId;
            quantity.disabled = !maxQuantity;
            quantity.max = maxQuantity;
            quantity.value = maxQuantity;
            help.textContent = maxQuantity ? `Maximum available: ${maxQuantity} pcs` : 'Choose a production lot first.';
        });
    </script>
@endpush