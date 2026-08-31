@extends('layouts.app')
@section('title', 'Clone BOM')

@section('content')
<div class="container">

    <h5 class="mb-3">
        Clone BOM – {{ $bom->product->our_part_no }} (v{{ $bom->version }})
    </h5>

    <div class="alert alert-info">
        This will create a <strong>new BOM version</strong> for future production.
        Existing batches will NOT be affected.
    </div>

    <form method="POST" action="{{ route('production.boms.clone.store', $bom->id) }}">
        @csrf

        <div class="mb-3">
            <label class="fw-bold">Remarks</label>
            <input type="text" name="remarks" class="form-control">
        </div>

        <button class="btn btn-success">
            <i class="fa fa-check"></i> Create New Version
        </button>

        <a href="{{ route('production.boms.index') }}" class="btn btn-secondary">
            Cancel
        </a>
    </form>

</div>
@endsection
