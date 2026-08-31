@extends('layouts.app')
@section('title')
    My Profile
@endsection
@section('content')
    <div class="mb-3">
        <h5 class="my-primary-color">My Profile</h5>
        <small class="text-muted">
            <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
            <i class="fa-solid fa-angle-right"></i>  My Profile
        </small>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="w-75 card">
                <div class="card-header">
                    <h5>Account</h5>
                </div>
                <div class="container">
                    <form action="{{ route('profile.update', ['id' => auth()->user()->id]) }}" method="POST">
                        @csrf
                        @method('PUT') <!-- If updating existing data -->
                        <div class="mb-2">
                            <label for="name" class="form-label">Name</label>
                            <input class="form-control" type="text" name="name" id="name"
                                value="{{ old('name', auth()->user()->name) }}" required>
                        </div>
                        <div class="mb-2">
                            <label for="email" class="form-label">Email</label>
                            <input class="form-control" type="email" name="email" id="email"
                                value="{{ old('email', auth()->user()->email) }}" disabled>
                        </div>
                        <div class="mb-2">
                            <label for="telephone" class="form-label">Telephone</label>
                            <input class="form-control" type="text" name="telephone" id="telephone"
                                value="{{ old('phone', auth()->user()->telephone) }}">
                        </div>
                        <div class="mb-2">
                            <label for="role" class="form-label">Role</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->role }}" disabled>
                        </div>
                        <div class="mb-2">
                            <button type="submit" class="btn btn1">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="w-75 card">
                <div class="card-header">
                    <h5>Change Password</h5>
                </div>
                <div class="container">
                    <form method="POST" action="{{ route('password.change', ['id' => auth()->user()->id]) }}">
                        @csrf
                        @method('PUT') <!-- or POST based on your route -->
                        <div class="mb-3">
                            <label for="old_password" class="form-label">Old Password*</label>
                            <input type="password" class="form-control" name="old_password" id="old_password"
                                placeholder="Enter Old Password" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password*</label>
                            <input type="password" class="form-control" name="new_password" id="new_password"
                                placeholder="Enter New Password" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password*</label>
                            <input type="password" class="form-control" name="new_password_confirmation"
                                id="new_password_confirmation" placeholder="Enter Confirm Password" required>
                        </div>
                        <div class="mb-2">
                            <button type="submit" class="btn btn1">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <!-- Handling client side phone no validation -->
    <script>
        const input = document.getElementById('telephone');
        if (input) {
            input.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, 10);
            });
        }
    </script>
@endpush