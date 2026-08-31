<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    /*----------------------------for user registration--------------------------------- */

    public function showRegister()
{
    return view('login.register');
}

public function register(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'username' => 'required|unique:user',
        'email' => 'required|email|unique:user',
        'password' => 'required|min:6|confirmed',
        'role' => 'required|in:admin,client,developer',
        'designation' => 'nullable',
    ]);

    User::create([
        'name' => $request->name,
        'username' => $request->username,
        'email' => $request->email,
        'role' => $request->role,
        'designation' => $request->designation,
        'password' => Hash::make($request->password),
    ]);

    return redirect('/login')->with('success', 'Registered successfully.');
}

/*------------------------------------For user login-------------------------------------*/

    public function showLogin()
    {
        return view('login.login');
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No account found with this email.']);
        }

        if (!$user->login_enabled) {
            return back()->withErrors(['email' => 'Your account is currently disabled.']);
        }

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return back()->withErrors(['email' => 'Invalid password.']);
        }

        $request->session()->regenerate();
        $user->update(['last_login' => now()]);

        return redirect()->intended('/dashboard');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

}
