@extends('layouts.login-register-layout')
@section('title')
    Login
@endsection
@section('content')
    <div class="container d-flex flex-column justify-content-center align-items-center">
        {{-- <img class="m-0" src="{{ Vite::asset('resources/images/tetech-logo.png') }}"> --}}
        <img class="m-0" src="/images/client-logo.png">
        <div class="container d-flex flex-column justify-content-center align-items-center h-100 gap-3">
            <h2 class="mt-2">Login</h2>
            <div class="form w-75 ">
                <form method="POST" action="/login" class="mb-3">
                    @csrf
                    <label htmlFor="email">Email</label>
                    <input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
                    <label aria-label="password">Password</label>
                    <input class="form-control mb-2" type="password" name="password" placeholder="Password" required>
                    <p><a class="float-end" href="#">Forgot Password?</a></p>
                    <button class="btn btn-login" type="submit">Login</button>
                </form>
                {{-- <p>Don't have an account? <a href="/register" class="register-link">Register</span></p> --}}
            </div>
        </div>
    </div>
@endsection