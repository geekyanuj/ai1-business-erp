@php
    /*
        Expected variables:
        $index  → branch array index
        $branch → branch data array or model (optional)
    */
@endphp

<div class="card mb-2 branch-row">
    <div class="card-body row g-2">

        {{-- Branch Name --}}
        <div class="col-md-3">
            <label class="form-label required-field">Branch Name</label>
            <input type="text"
                   class="form-control"
                   name="branches[{{ $index }}][name]"
                   value="{{ old("branches.$index.name", $branch['name'] ?? '') }}"
                   required>
        </div>

        {{-- Branch Code --}}
        <div class="col-md-3">
            <label class="form-label required-field">Branch Code</label>
            <input type="text"
                   class="form-control"
                   name="branches[{{ $index }}][branch_code]"
                   value="{{ old("branches.$index.branch_code", $branch['branch_code'] ?? '') }}"
                   required>
        </div>

        {{-- GST --}}
        <div class="col-md-3">
            <label class="form-label required-field">GST Number</label>
            <input type="text"
                   class="form-control"
                   name="branches[{{ $index }}][gst_number]"
                   value="{{ old("branches.$index.gst_number", $branch['gst_number'] ?? '') }}"
                   required>
        </div>

        {{-- State Code --}}
        <div class="col-md-3">
            <label class="form-label required-field">State Code</label>
            <input type="text"
                   class="form-control"
                   name="branches[{{ $index }}][state_code]"
                   value="{{ old("branches.$index.state_code", $branch['state_code'] ?? '') }}"
                   required>
        </div>


        {{-- Address Line 1 --}}
        <div class="col-md-4">
            <label class="form-label required-field">Address Line 1</label>
            <input type="text"
                   class="form-control"
                   name="branches[{{ $index }}][address_line1]"
                   value="{{ old("branches.$index.address_line1", $branch['address_line1'] ?? '') }}"
                   required>
        </div>

        {{-- Address Line 2 --}}
        <div class="col-md-4">
            <label class="form-label required-field">Address Line 2</label>
            <input type="text"
                   class="form-control"
                   name="branches[{{ $index }}][address_line2]"
                   value="{{ old("branches.$index.address_line2", $branch['address_line2'] ?? '') }}"
                   required>
        </div>

        {{-- City --}}
        <div class="col-md-2">
            <label class="form-label required-field">City</label>
            <input type="text"
                   class="form-control"
                   name="branches[{{ $index }}][city]"
                   value="{{ old("branches.$index.city", $branch['city'] ?? '') }}"
                   required>
        </div>

        {{-- State --}}
        <div class="col-md-2">
            <label class="form-label required-field">State</label>
            <input type="text"
                   class="form-control"
                   name="branches[{{ $index }}][state]"
                   value="{{ old("branches.$index.state", $branch['state'] ?? '') }}"
                   required>
        </div>

        {{-- Pincode --}}
        <div class="col-md-1">
            <label class="form-label required-field">Pincode</label>
            <input type="text"
                   class="form-control"
                   name="branches[{{ $index }}][pincode]"
                   value="{{ old("branches.$index.pincode", $branch['pincode'] ?? '') }}"
                   required>
        </div>

        {{-- Country --}}
        <div class="col-md-1">
            <label class="form-label required-field">Country</label>
            <input type="text"
                   class="form-control"
                   name="branches[{{ $index }}][country]"
                   value="{{ old("branches.$index.country", $branch['country'] ?? '') }}"
                   required>
        </div>

        {{-- Phone --}}
        <div class="col-md-2">
            <label class="form-label">Phone</label>
            <input type="text"
                   class="form-control"
                   name="branches[{{ $index }}][phone]"
                   value="{{ old("branches.$index.phone", $branch['phone'] ?? '') }}">
        </div>

        {{-- Email --}}
        <div class="col-md-3">
            <label class="form-label">Email</label>
            <input type="email"
                   class="form-control"
                   name="branches[{{ $index }}][email]"
                   value="{{ old("branches.$index.email", $branch['email'] ?? '') }}">
        </div>

        {{-- Default --}}
        <div class="col-md-2 d-flex align-items-end">
            <div class="form-check">
                <input type="radio"
                       class="form-check-input"
                       name="default_branch"
                       value="{{ $index }}"
                       {{ old('default_branch') == $index || (!old('default_branch') && ($branch['is_default'] ?? false)) ? 'checked' : '' }}>
                <label class="form-check-label">Default</label>
            </div>
        </div>

        {{-- Active --}}
        <div class="col-md-2 d-flex align-items-end">
            <div class="form-check">
                <input type="checkbox"
                    class="form-check-input"
                    name="branches[{{ $index }}][is_active]"
                    value="1"
                    {{ old("branches.$index.is_active", $branch['is_active'] ?? false) ? 'checked' : '' }}>
                <label class="form-check-label">Active</label>
            </div>
        </div>


        {{-- Remove --}}
        <div class="col-md-1 d-flex align-items-end">
            <button type="button"
                    class="btn btn-danger btn-sm"
                    onclick="this.closest('.branch-row').remove()">×</button>
        </div>

    </div>
</div>
