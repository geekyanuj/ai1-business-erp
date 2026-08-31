@extends('layouts.app')
@section('title', 'BOM Details')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between mb-3">
        <h5>
            BOM – {{ $bom->product->our_part_no }} (v{{ $bom->version }})
        </h5>

        <a href="{{ route('production.boms.index') }}" class="btn btn-sm btn-secondary">
           <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <strong>Status:</strong>
            <span class="badge bg-{{ $bom->is_active ? 'success' : 'secondary' }}">
                {{ $bom->is_active ? 'Active' : 'Inactive' }}
            </span>
            <br>
            <strong>Remarks:</strong> {{ $bom->remarks ?? '-' }}
        </div>
    </div>

    <div class="card">
        <div class="card-header fw-bold">
            Materials
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Material</th>
                        <th width="150">Qty / Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bom->items as $item)
                        <tr>
                            <td>{{ $item->material_name }}</td>
                            <td>{{ $item->quantity_per_unit }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('production.boms.clone', $bom->id) }}"
           class="btn btn-warning">
            <i class="fa fa-copy"></i> Clone as New Version
        </a>
    </div>

</div>
@endsection
