@props([
    'address' => null,
    'required' => true,
    'formId' => 'addressForm',
    'title' => 'Address Details',
    'targetSelectId' => '',
    'offcanvasId' => '',
])

<form id="{{ $formId }}" data-target-select="{{ $targetSelectId }}" data-offcanvas-id="{{ $offcanvasId }}">

    @csrf

    <div class="col-md-12">

        <h5 class="mb-3">{{ $title }}</h5>

        {{-- Inline feedback --}}
        <div id="{{ $formId }}-status" class="mb-2" style="display:none;"></div>

        <div class="row g-2">

            <input type="hidden" name="id" value="{{ $address->id ?? '' }}">

            <!-- Address Line 1 -->
            <div class="col-md-12">
                <label class="form-label {{ $required ? 'required-field' : '' }}">
                    Address Line 1
                </label>
                <input type="text" name="address_line_1" class="form-control form-control-sm"
                    value="{{ $address->address_line_1 ?? '' }}" {{ $required ? 'required' : '' }}>
            </div>

            <!-- Address Line 2 -->
            <div class="col-md-12">
                <input type="text" name="address_line_2" class="form-control form-control-sm"
                    placeholder="Address Line 2" value="{{ $address->address_line_2 ?? '' }}">
            </div>

            <!-- City -->
            <div class="col-md-4">
                <input type="text" name="city" class="form-control form-control-sm" placeholder="City"
                    value="{{ $address->city ?? '' }}" {{ $required ? 'required' : '' }}>
            </div>

            <!-- State -->
            <div class="col-md-4">
                <input type="text" name="state" class="form-control form-control-sm" placeholder="State"
                    value="{{ $address->state ?? '' }}">
            </div>

            <!-- Postal Code -->
            <div class="col-md-4">
                <input type="text" name="postal_code" class="form-control form-control-sm" placeholder="Postal Code"
                    value="{{ $address->postal_code ?? '' }}">
            </div>

            <!-- Country -->
            <div class="col-md-12">
                <input type="text" name="country" class="form-control form-control-sm" placeholder="Country"
                    value="{{ $address->country ?? '' }}" {{ $required ? 'required' : '' }}>
            </div>

            <!-- Save Button -->
            <div class="col-md-12 mt-3">
                <button type="submit" class="btn btn-sm btn-primary">
                    <span class="save-text">Save Address</span>
                    <span class="save-spinner d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span> Saving...
                    </span>
                </button>
            </div>

        </div>
    </div>

</form>
