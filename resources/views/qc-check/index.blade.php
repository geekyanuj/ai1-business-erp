@extends('layouts.app')
@section('title')
    Quality Check
@endsection

@section('content')
    <div class="mb-3">
        <h5 class="my-primary-color">Quality Check</h5>
        <small class="text-muted">
            <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
            <i class="fa-solid fa-angle-right"></i> Quality Check
        </small>
    </div>
    <div class="card">
        <div class="card-header">
            Batch Processing
        </div>
        <div class="card-body">
            <form action="/qc-upload" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="excel-upload">
                            <label for="excel-label" class="required-field">Excel File</label>
                            <input class="form-control" type="file" name="excel" required>
                        </div>
                        <div class="pdfs-upload mb-2">
                            <label for="pdf-label" class="required-field">Pdfs</label>
                            <input class="form-control" type="file" name="pdfs[]" multiple required>
                        </div>
                        <button class="btn btn-sm btn-secondary" type="submit">Upload</button>
                    </div>
                    <div class="col-md-8 mb-2">
                        <div class="card">
                            <div class="card-header card-header-sm">
                                Important Note
                            </div>
                            <div class="card-body card-body-sm">
                                📋 QC Upload Instructions :
                                <ul>
                                    <li> Upload one Excel file with columns:
                                        Filename | Serial No | Part No | Date</li>

                                    <li>Upload all matching PDFs listed in the Excel.
                                        (Filename must match exactly)</li>

                                    <li>QC footer will be added to the last page only.</li>

                                    <li>Processed PDFs will be saved automatically after upload.</li>

                                    <li>At a time only 18 files can be processed (By default).</li>
                                </ul>
                                <small>⚠️ If any PDF is missing or data is invalid, errors will be occured.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>



    @if ($errors->any())
        <div style="color:red; border:1px solid red; padding:10px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

@endsection

@push('scripts')
<script>
    // Always reset UI on fresh load
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
                window.location.reload();
            }
        });
</script>
@endpush
