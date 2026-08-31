@extends('layouts.app')
@section('title', 'Users')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div>
                <!-- Page Header -->
                <div class="d-flex align-items-center mb-0">
                    <div>
                        <h5 class="my-primary-color">Users</h5>
                        <small class="text-muted">
                            <a class="text-decoration-none" href="{{ route('dashboard') }}">Home</a>
                            <i class="fa-solid fa-angle-right"></i> Users
                        </small>
                    </div>
                </div>

                <!-- Add User Button -->
                <div class="d-flex justify-content-end mb-2">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fa-solid fa-plus"></i> Add User
                    </button>
                </div>

                <!-- Users Table -->
                <div class="card">
                    <div class="card-body my-0 py-0">

                    
                    <table id="usersTable" class="table table-sm table-bordered table-striped"
                        data-url="{{ route('users.data') }}">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Telephone</th>
                                <th>Last Login</th>
                                <th>Login Enable</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="addUserForm" action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="modal-header py-2">
                        <h5 class="modal-title my-primary-color" id="addUserModalLabel">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label mb-1 required-field">Full Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="telephone" class="form-label mb-1">Telephone</label>
                                <input type="text" name="telephone" id="telephone" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label mb-1 required-field">Email</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="role" class="form-label mb-1 required-field">Role/Department</label>
                                <select name="role" id="role" class="form-select" required>
                                    <option value="Sales" selected>Sales</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Warehouse">Warehouse</option>
                                    <option value="Production">Production</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <label for="login_enabled" class="form-label me-3 mb-0">Login Enable</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="login_enabled" name="login_enabled"
                                        checked>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label mb-1 required-field">Password</label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm px-3">
                            <i class="fas fa-save me-1"></i> Save User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="editUserForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header py-2">
                        <h5 class="modal-title my-primary-color" id="editUserModalLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editUserId">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label mb-1 required-field">Name</label>
                                <input type="text" name="name" id="editUserName" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-1 required-field">Email</label>
                                <input type="email" name="email" id="editUserEmail" class="form-control"
                                    @if(auth()->user()->role !== 'Admin') readonly disabled @endif>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-1 required-field">Role/Department</label>
                                <select name="role" id="editUserRole" class="form-select" required
                                    @if(auth()->user()->role !== 'Admin') disabled @endif>
                                    <option value="Sales" selected>Sales</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Warehouse">Warehouse</option>
                                    <option value="Production">Production</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-1">Telephone</label>
                                <input type="text" name="telephone" id="editUserTelephone"
                                    class="form-control">
                            </div>
                            <div class="col-md-6 d-flex align-items-center mt-3">
                                <label for="editUserLogin_enabled" class="form-label me-3 mb-0">Login Enable</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="login_enabled" value="0">
                                    <input class="form-check-input" type="checkbox" id="editUserLogin_enabled"
                                        name="login_enabled" value="1" @if(auth()->user()->role !== 'Admin') disabled @endif>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center mt-3">
                                <label for="resetPasswordToggle" class="form-label me-3 mb-0">Reset Password</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="resetPasswordToggle">
                                </div>
                            </div>
                            <div class="col-md-12 mt-2" id="editPasswordSection" style="display:none;">
                                <label class="form-label mb-1">New Password</label>
                                <input type="password" name="password" id="editPassword"
                                    class="form-control" placeholder="Enter new password">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm px-3">Update User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/users.js')
@endpush