@extends('layouts.app')
@section('title', 'General Settings')

@section('content')
    <div class="mb-3">
        <h5 class="my-primary-color">General Settings</h5>
        <small class="text-muted">
            <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
            <i class="fa-solid fa-angle-right"></i> General Settings
        </small>
    </div>

    <div class="row d-flex justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold">Tax Settings</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.general.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">CGST Division Percentage (%)</label>
                                <input type="number" name="settings[cgst_division_percentage]" class="form-control" 
                                    value="{{ \App\Models\Setting::get('cgst_division_percentage', '50') }}" step="0.01" min="0" max="100" required>
                                <small class="text-muted">Percentage of total tax to be allocated to CGST (e.g., 50 for 50/50 split)</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">SGST Division Percentage (%)</label>
                                <input type="number" name="settings[sgst_division_percentage]" class="form-control" 
                                    value="{{ \App\Models\Setting::get('sgst_division_percentage', '50') }}" step="0.01" min="0" max="100" required>
                                <small class="text-muted">Percentage of total tax to be allocated to SGST (e.g., 50 for 50/50 split)</small>
                            </div>
                        </div>

                        <hr>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn bg-my-primary text-white">Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
