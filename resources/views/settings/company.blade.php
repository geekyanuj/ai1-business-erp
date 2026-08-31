@extends('layouts.app')
@section('title', 'Company Settings')

@section('content')
    <div class="mb-3">
        <h5 class="my-primary-color">Company Profile</h5>
        <small class="text-muted">
            <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
            <i class="fa-solid fa-angle-right"></i>  Company Profile
        </small>
    </div>

    <div class="row d-flex justify-content-center container">

        <div class="card shadow col-md-10">
            <div class="card-body">

                <form method="POST" action="{{ $company ? route('company.update', $company) : route('company.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    @if($company) @method('PUT') @endif

                    {{-- ================= COMPANY ================= --}}
                    <h6 class="fw-bold">Company Details</h6>
                    <div class="row g-3">

                        <div class="col-md-12 text-center">
                            <img id="logoPreview" src="{{ $company?->logo ? asset('storage/' . $company->logo) : '' }}"
                                height="70" style="{{ $company?->logo ? '' : 'display:none;' }}">
                        </div>

                        <div class="col-md-6">
                            <label class="required-field">Company Name</label>
                            <input name="name" class="form-control" value="{{ old('name', $company->name ?? '') }}"
                                required>
                        </div>

                        <div class="col-md-3">
                            <label class="required-field">Company Code</label>
                            <input name="company_code" class="form-control"
                                value="{{ old('company_code', $company->company_code ?? '') }}" maxlength="4" required>
                        </div>

                        <div class="col-md-3">
                            <label class="required-field">PAN</label>
                            <input name="pan_number" class="form-control"
                                value="{{ old('pan_number', $company->pan_number ?? '') }}" maxlength="10" required>
                        </div>

                        <div class="col-md-3">
                            <label>CIN</label>
                            <input name="cin_number" class="form-control"
                                value="{{ old('cin_number', $company->cin_number ?? '') }}">
                        </div>

                        <div class="col-md-3">
                            <label>IEC</label>
                            <input name="iec_number" class="form-control"
                                value="{{ old('iec_number', $company->iec_number ?? '') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="required-field">Email</label>
                            <input name="email" class="form-control" value="{{ old('email', $company->email ?? '') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="required-field">Phone</label>
                            <input name="phone" class="form-control" value="{{ old('phone', $company->phone ?? '') }}"
                                maxlength="10" required>
                        </div>

                        <div class="col-md-6">
                            <label class="required-field">Logo</label>
                            <input type="file" name="logo" class="form-control" {{ $company ? '' : 'required' }}
                                onchange="previewLogo(event)">
                        </div>

                        <div class="col-md-6">
                            <label class="required-field">Authorised Signature</label>
                            <input type="file" name="authorised_signature" class="form-control" {{ $company ? '' : 'required' }}
                                onchange="previewSignature(event)">
                        </div>

                        <div class="col-md-12 text-center">
                            <img id="signaturePreview" src="{{ $company?->authorised_signature ? asset('storage/' . $company->authorised_signature) : '' }}"
                                height="70" style="{{ $company?->authorised_signature ? '' : 'display:none;' }}">
                        </div>
                    </div>

                    <hr>

                    {{-- ================= BRANCHES ================= --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold required-field">Branches</h6>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addBranch()">+ Add Branch</button>
                    </div>

                    <div id="branchesWrapper" class="mt-3">
                        @php
                            $branches = old('branches', $company?->branches?->toArray() ?? []);
                        @endphp

                        @foreach($branches as $i => $branch)
                            @include('settings.partials.branch-row', ['index' => $i, 'branch' => $branch])
                        @endforeach
                    </div>

                    <button class="btn bg-my-primary text-white mt-3">Save Company</button>
                </form>
            </div>
        </div>

    </div>

    <template id="branch-template">
        <div class="card mb-2 branch-row">
            <div class="card-body row g-2">

                <div class="col-md-3">
                    <label class="form-label required-field">Branch Name</label>
                    <input type="text" class="form-control" name="branches[__INDEX__][name]" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label required-field">Branch Code</label>
                    <input type="text" class="form-control" name="branches[__INDEX__][branch_code]" maxlength="4" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label required-field">GST Number</label>
                    <input type="text" class="form-control" name="branches[__INDEX__][gst_number]" maxlength="15" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label required-field">State Code</label>
                    <input type="text" class="form-control" name="branches[__INDEX__][state_code]" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label required-field">Address Line 1</label>
                    <input type="text" class="form-control" name="branches[__INDEX__][address_line1]" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label required-field">Address Line 2</label>
                    <input type="text" class="form-control" name="branches[__INDEX__][address_line2]" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label required-field">City</label>
                    <input type="text" class="form-control" name="branches[__INDEX__][city]" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label required-field">State</label>
                    <input type="text" class="form-control" name="branches[__INDEX__][state]" required>
                </div>

                <div class="col-md-1">
                    <label class="form-label required-field">Pincode</label>
                    <input type="text" class="form-control" name="branches[__INDEX__][pincode]" maxlength="6" required>
                </div>

                <div class="col-md-1">
                    <label class="form-label required-field">Country</label>
                    <input type="text" class="form-control" name="branches[__INDEX__][country]" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" name="branches[__INDEX__][phone]" maxlength="10">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="branches[__INDEX__][email]">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check">
                        <input type="radio" class="form-check-input" name="default_branch" value="__INDEX__">
                        <label class="form-check-label">Default</label>
                    </div>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="branches[__INDEX__][is_active]" value="1">
                        <label class="form-check-label">Active</label>
                    </div>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm"
                        onclick="this.closest('.branch-row').remove()">×</button>
                </div>

            </div>
        </div>
    </template>


@endsection

@push('scripts')
    <script>
        let branchIndex = {{ count($branches) }};

        function addBranch() {
            const template = document.getElementById('branch-template').innerHTML;
            const html = template.replaceAll('__INDEX__', branchIndex);

            document.getElementById('branchesWrapper')
                .insertAdjacentHTML('beforeend', html);

            branchIndex++;
        }


        function previewLogo(e) {
            const img = document.getElementById('logoPreview');
            img.src = URL.createObjectURL(e.target.files[0]);
            img.style.display = 'block';
        }

        function previewSignature(e) {
            const img = document.getElementById('signaturePreview');
            img.src = URL.createObjectURL(e.target.files[0]);
            img.style.display = 'block';
        }
    </script>
@endpush