<div class="inventory-section" data-type="{{ $type }}">

    {{-- INTERNAL FILTERS --}}
    <div class="row mb-2">
        <div class="col-md-4">
            <select class="form-select form-select-sm location-filter">
                <option value="">All Locations</option>
                <option value="main warehouse">Main Warehouse</option>
                <option value="store">Store</option>
            </select>
        </div>
    </div>

    {{-- GRID --}}
    <div class="row g-3 inventory-grid"
         data-type="{{ $type }}"
         data-url="{{ route('inventory.datatable') }}">
    </div>

</div>
