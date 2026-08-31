@extends('layouts.app')

@section('title', 'Label Studio')

@section('content')
<div class="container-fluid label-studio py-2">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <div class="studio-kicker"><i class="fa-solid fa-satellite-dish me-1"></i> Print control center</div>
            <h2 class="mb-1">Label Studio</h2>
            <p class="text-muted mb-0">Compose one print run from any production lot, product, or category.</p>
        </div>
        <div class="studio-counter"><span id="selectedCount">0</span> selected / {{ $labelCount }} available</div>
    </div>

    <form method="GET" action="{{ route('labels.studio') }}" class="studio-filters mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-lg-2 col-md-4">
                <label for="studioCategory">Category</label>
                <select id="studioCategory" name="category" class="form-select form-select-sm">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 col-md-4">
                <label for="studioProduct">Product</label>
                <select id="studioProduct" name="product_id" class="form-select form-select-sm">
                    <option value="">All products</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-category="{{ $product->category }}" @selected((string) request('product_id') === (string) $product->id)>{{ $product->our_part_no }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 col-md-4">
                <label for="studioLot">Lot contains</label>
                <input id="studioLot" name="lot_no" value="{{ request('lot_no') }}" class="form-control form-control-sm" placeholder="Search lot number">
            </div>
            <div class="col-lg-2 col-md-6">
                <label for="studioLabel">Label batch</label>
                <select id="studioLabel" name="label_id" class="form-select form-select-sm">
                    <option value="">All batches</option>
                    @foreach ($labels as $label)
                        <option value="{{ $label->id }}" @selected((string) request('label_id') === (string) $label->id)>{{ $label->lot_no }} · {{ $label->client->name ?? 'No client' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-6 d-flex gap-2">
                <button class="btn btn-dark btn-sm flex-grow-1"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                <a href="{{ route('labels.studio') }}" class="btn btn-outline-secondary btn-sm" title="Clear filters"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </div>
    </form>

    <section class="studio-panel preview-panel mb-3" aria-labelledby="previewTitle">
        <div class="panel-header d-flex justify-content-between align-items-center">
            <div><strong id="previewTitle">Live print preview</strong><small class="d-block text-muted">Only the labels shown here will be sent to the printer.</small></div>
            <span class="preview-size" id="previewSize">A4 · 2 × 4</span>
        </div>
        <div id="previewGrid" class="preview-grid">
            <div class="preview-empty"><i class="fa-solid fa-eye-slash"></i><span>Select labels to preview the sheet.</span></div>
        </div>
    </section>

    <form method="POST" action="{{ route('labels.studio.print') }}" target="_blank" id="studioPrintForm">
        @csrf
        <div class="row g-3">
            <div class="col-xl-9">
                <div class="studio-panel">
                    <div class="panel-header d-flex justify-content-between align-items-center">
                        <div><strong>Available labels</strong><small class="d-block text-muted">Select individual serial labels across any category.</small></div>
                        <button type="button" class="btn btn-outline-dark btn-sm" id="toggleAll"><i class="fa-solid fa-check-double me-1"></i> Select visible</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="studioLabelsTable" data-url="{{ route('labels.studio.items', request()->query()) }}">
                            <thead><tr><th class="check-col"></th><th>Part number</th><th>Category</th><th>Lot</th><th>Client</th><th>Item code</th></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="studio-panel print-panel">
                    <div class="panel-header"><strong>Page composition</strong><small class="d-block text-muted">Choose the physical sheet before printing.</small></div>
                    <div class="p-3">
                        <label>Print type</label>
                        <div class="mode-switch mb-3">
                            <label><input type="radio" name="mode" value="unit" checked><span><i class="fa-solid fa-tag"></i> Unit labels</span></label>
                            <label><input type="radio" name="mode" value="box"><span><i class="fa-solid fa-box"></i> Box labels</span></label>
                        </div>
                        <div id="boxConfig" class="box-config mb-3 d-none">
                            <label>Box structure</label>
                            <div id="boxRows" class="small text-muted">Select labels to configure boxes by product.</div>
                        </div>
                        <label>Fields to print</label>
                        <div class="field-grid mb-3">
                            @foreach ([
                                'part_no' => 'TE part number',
                                'client_name' => 'Client name',
                                'client_part_no' => 'Client part number',
                                'lot_no' => 'Lot number',
                                'item_code' => 'Serial / box items',
                                'quantity' => 'Quantity',
                                'description' => 'Description',
                                'qr' => 'QR code',
                                'artwork' => 'Logo & marks',
                            ] as $field => $fieldLabel)
                                <label class="field-option"><input type="checkbox" name="fields[]" value="{{ $field }}" checked><span>{{ $fieldLabel }}</span></label>
                            @endforeach
                        </div>
                        <label>Sheet size</label>
                        <div class="size-switch mb-3">
                            <label><input type="radio" name="page_size" value="A4"><span>A4 <small>210 × 297</small></span></label>
                            <label><input type="radio" name="page_size" value="A3" checked><span>A3 <small>297 × 420</small></span></label>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6"><label for="studioColumns">Columns</label><input id="studioColumns" name="columns" type="number" min="1" max="6" value="2" class="form-control"></div>
                            <div class="col-6"><label for="studioRows">Rows</label><input id="studioRows" name="rows" type="number" min="1" max="10" value="5" class="form-control"></div>
                        </div>
                        <div class="capacity-box mb-3"><i class="fa-solid fa-table-cells-large"></i><span><b id="capacity">10</b> labels per sheet</span></div>
                        <button class="btn btn-primary w-100" id="printSelected" disabled><i class="fa-solid fa-print me-1"></i> Print selected labels</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .label-studio { --ink:#161616; --accent:#ff8000; --line:#dedede; background:linear-gradient(135deg,#fafcf2 0%,#f2f3e9 100%); min-height:100%; min-width:0; overflow-x:hidden; }
    .studio-kicker { color:var(--accent); font-size:.72rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; }
    .label-studio h2 { color:var(--ink); font-weight:800; letter-spacing:0; }
    .studio-counter { border:1px solid #f2bd86; color:#a94e00; background:#fff4e8; padding:.45rem .7rem; font-size:.82rem; font-weight:700; }
    .studio-filters,.studio-panel { background:#fff; border:1px solid var(--line); box-shadow:0 8px 24px rgba(23,33,43,.05); }
    .studio-filters { padding:1rem; }
    .studio-filters label,.print-panel label { color:#5d6b73; font-size:.72rem; font-weight:700; margin-bottom:.3rem; text-transform:uppercase; }
    .panel-header { border-bottom:1px solid var(--line); padding:1rem 1.1rem; color:var(--ink); }
    .panel-header small { font-size:.75rem; font-weight:400; }
    .check-col { width:44px; }
    .category-chip { background:#fff0df; color:#a94e00; font-size:.72rem; padding:.22rem .45rem; }
    .table thead th { background:#f5f8f9; border-bottom:1px solid var(--line); color:#607078; font-size:.7rem; text-transform:uppercase; }
    .table td { font-size:.82rem; border-color:#edf1f2; }
    .size-switch { display:flex; gap:.5rem; }
    .size-switch label { flex:1; margin:0; }
    .size-switch input { position:absolute; opacity:0; }
    .size-switch span { display:block; border:1px solid var(--line); padding:.65rem .5rem; color:var(--ink); cursor:pointer; text-align:center; text-transform:none; font-size:.85rem; }
    .size-switch small { display:block; color:#7c8a90; font-size:.65rem; font-weight:400; }
    .size-switch input:checked + span { border-color:var(--accent); box-shadow:inset 0 -3px var(--accent); background:#fff8f0; }
    .capacity-box { align-items:center; background:#f4f8f8; color:#52636a; display:flex; gap:.6rem; padding:.7rem; }
    .capacity-box i { color:var(--accent); font-size:1.2rem; }
    .capacity-box b { color:var(--ink); }
    .mode-switch { display:flex; gap:.5rem; }
    .mode-switch label { flex:1; margin:0; }
    .mode-switch input { position:absolute; opacity:0; }
    .mode-switch span { display:block; border:1px solid var(--line); padding:.55rem .35rem; color:var(--ink); cursor:pointer; text-align:center; text-transform:none!important; font-size:.76rem!important; }
    .mode-switch input:checked + span { border-color:var(--accent); background:#fff8f0; color:#a94e00; }
    .box-config { border-left:3px solid var(--accent); background:#fff8f0; padding:.65rem; }
    .box-row-config { align-items:center; display:flex; gap:.5rem; justify-content:space-between; margin-top:.45rem; }
    .box-row-config span { color:var(--ink); font-size:.75rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .box-row-config input { max-width:72px; }
    .field-grid { display:grid; gap:.35rem; grid-template-columns:1fr 1fr; }
    .field-option { align-items:center; display:flex; gap:.35rem; margin:0!important; text-transform:none!important; color:var(--ink)!important; font-size:.7rem!important; font-weight:500!important; }
    .field-option input { accent-color:var(--accent); }
    #studioLabelsTable_wrapper { max-width:100%; overflow:hidden; padding:0 1rem 1rem; }
    #studioLabelsTable { width:100%!important; }
    #studioLabelsTable_wrapper .dataTables_scrollBody { overflow-x:auto!important; }
    #studioLabelsTable_wrapper .dataTables_length, #studioLabelsTable_wrapper .dataTables_filter { padding-top:.75rem; }
    #studioLabelsTable_wrapper .dataTables_paginate .paginate_button.current { background:var(--accent)!important; border-color:var(--accent)!important; color:#fff!important; }
    .preview-panel { overflow:hidden; }
    .preview-size { color:#a94e00; background:#fff0df; font-size:.72rem; font-weight:700; padding:.35rem .55rem; }
    .preview-grid { display:grid; gap:1rem; grid-template-columns:repeat(auto-fill,minmax(230px,1fr)); padding:1rem; }
    .preview-empty { align-items:center; color:#788187; display:flex; flex-direction:column; gap:.5rem; grid-column:1/-1; justify-content:center; min-height:120px; }
    .preview-empty i { color:var(--accent); font-size:1.6rem; }
    .label-preview { background:#fff; border:1px solid #bfc6c8; box-shadow:0 5px 14px rgba(22,22,22,.08); min-height:145px; padding:.65rem; position:relative; }
    .label-preview img.logo-preview { height:24px; object-fit:contain; position:absolute; right:.5rem; top:.5rem; width:72px; }
    .preview-part { color:var(--ink); font-size:.86rem; font-weight:800; padding-right:78px; }
    .preview-line { color:#536067; font-size:.7rem; line-height:1.45; }
    .preview-line strong { color:var(--ink); }
    .preview-code { border-top:1px solid #e5e7e8; color:#a94e00; font-family:monospace; font-size:.7rem; margin-top:.35rem; padding-top:.35rem; }
    .preview-art { bottom:.4rem; display:flex; gap:.25rem; position:absolute; right:.5rem; }
    .preview-art img { height:15px; object-fit:contain; width:15px; }
    @media(max-width:575px){ .label-studio { padding-left:.5rem; padding-right:.5rem; } .table { min-width:720px; } }
</style>
@endpush

@push('scripts')
    @vite('resources/js/pages/label-studio.js')
@endpush
