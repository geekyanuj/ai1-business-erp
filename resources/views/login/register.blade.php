@extends('layouts.login-register-layout')
@section('title')
    Register
@endsection
@section('content')
    <div class="container d-flex flex-column justify-content-center align-items-center h-100 gap-3">
        <h2>Register</h2>
        <div class="form w-75">
            <form method="POST" action="/register">
                @csrf
                <label htmlFor="name" for="name">Name</label>
                <input class="form-control mb-2" type="text" name="name" placeholder="Full Name" required>
                <label htmlFor="username" for="username">Username</label>
                <input class="form-control mb-2" type="text" name="username" placeholder="Username" required>
                <label htmlFor="email" for="email">Email</label>
                <input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
                <label htmlFor="role" for="role">Role</label>
                <select class="form-control mb-2" name="role" required>
                    <option value="Project Manager">Project Manager</option>
                    <option value="Developer">Developer</option>
                    <option value="Admin">Admin</option>
                </select>
                <label htmlFor="designation" for="designation">Designation</label>
                <select class="form-control mb-2" name="designation">
                    <option value="">--Designation (optional)--</option>
                    <option value="Associate Product Manager">Associate Product Manager</option>
                    <option value="Product Manager">Product Manager</option>
                    <option value="Senior Product Manager">Senior Product Manager</option>
                </select>

                <label htmlFor="password" for="password">Password</label>
                <input class="form-control mb-2" type="password" name="password" placeholder="Password" required>
                <label htmlFor="password_confirmation" for="password_confirmation">Confirm Password</label>
                <input class="form-control mb" type="password" name="password_confirmation" placeholder="Confirm Password"
                    required><br>
                <button class="btn btn-login" type="submit">Register</button>
            </form>
            <p>Already have an account? <a href="/login" class="register-link">Login</span></p>
        </div>
    </div>
    @if (session('success'))
        <div id="success-message"
            style="
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #d4edda;
        color: #155724;
        padding: 15px 20px;
        border-left: 5px solid #28a745;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        z-index: 9999;
        width:300px;
    ">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div id="error-message"
            style="
        position: fixed;
        top: 20px;
        right: 20px;
         width:300px;
        background-color: #f8d7da;
        color: #721c24;
        padding: 15px 20px;
        border-left: 5px solid #dc3545;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        z-index: 9999;
    ">
            {{ $errors->first() }}
        </div>
    @endif
@endsection
