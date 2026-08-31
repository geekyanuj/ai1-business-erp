@extends('layouts.app')
@section('title', 'Product Client Mapping')

@section('content')
<div class="mb-3">
    <h5 class="my-primary-color">Product – Client Mapping</h5>
</div>

{{-- Success --}}
<!-- @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif -->

<div class="card mb-4">
    <div class="card-body">
        <form method="POST" action="{{ route('product-client-mappings.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label required-field">Client</label>
                    <select name="client_id" class="form-select" id="addClient" required>
                        <!-- <option value="">Select Client</option> -->
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label required-field">Product (Our Part No)</label>
                    <select name="product_id" class="form-select" id="addProduct" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->our_part_no }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label required-field">Client Part No</label>
                    <input type="text" name="client_part_no" class="form-control">
                </div>

                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control"></textarea>
                </div>

                <div class="col-12 text-end">
                    <button class="btn btn-primary">Save Mapping</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Mapping List --}}
<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-sm mb-0" id="productClientMappingTable" data-url="{{ route('product-client-mappings.datatable') }}">
            <thead class="table-light">
                <tr>
                    <th class="text-center">Client</th>
                    <th class="text-center">Our Part No</th>
                    <th class="text-center">Description</th>
                    <th class="text-center">Client Part No</th>
                    <th width="80" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- @foreach($mappings as $map)
                    <tr>
                        <td class="text-center">{{ $map->client->name }}</td>
                        <td class="text-center">{{ $map->product->our_part_no }}</td>
                        <td class="text-center">{{ $map->product->description }}</td>
                        <td class="text-center">{{ $map->client_part_no }}</td>
                        <td class="text-center">
                            <form method="POST" action="{{ route('product-client-mappings.destroy', $map->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">X</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                @if($mappings->isEmpty())
                    <tr>
                        <td colspan="5" class="text-center text-muted">No mappings found</td>
                    </tr>
                @endif -->
            </tbody>
        </table>
    </div>
</div>
@endsection


@push('scripts')
<script>
    window.products = @json($products);
</script>
    @vite('resources/js/pages/product-client-mapping-index.js')
@endpush