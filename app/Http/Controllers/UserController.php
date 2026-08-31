<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function showProfile(string $id)
    {
        $user = User::findOrFail($id);
        return view('profile', ['user' => $user]);
    }
    public function updateProfile(Request $request, $id)
    {
        $user = User::findOrFail($id); // Get user by ID

        $request->validate([
            'name' => 'required|string',
            'telephone' => 'nullable|digits:10',
        ]);

        $user->name = $request->name;
        $user->telephone = $request->telephone;


        // Non-Admins keep existing role/designation
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    // change password function
    public function changePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'old_password' => 'required|current_password', // Laravel 9+ supports this rule
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();
        $user->password = bcrypt($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }



    // This function is used to display all users in the user index page
    public function index()
    {
        $users = User::all();
        return view('user.user-index');
    }


    //Insert the user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'telephone' => 'nullable|digits:10',
            'role' => 'required|in:Admin,Sales,Production,Warehouse',
            'password' => $request->login_enabled ? 'required|string|min:6' : 'nullable',
        ]);

        $user = new User($validated);
        $user->login_enabled = $request->login_enabled ? 1 : 0;

        if ($request->login_enabled && $request->filled('password')) {
            $user->password = bcrypt($request->password);
        } else {
            $user->password = ''; // or null, if database allows
        }

        $user->save();

        return redirect()->back()->with('success', 'User created successfully.');

    }

    public function getUsers(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select(['id', 'name', 'email', 'telephone', 'role', 'last_login', 'login_enabled']);
            return DataTables::of($data)
                ->addColumn('login_status', function ($user) {
                    return $user->login_enabled
                        ? '<i class="fas fa-check-circle text-success" title="Login Enabled"></i>'
                        : '<i class="fas fa-ban text-danger" title="Login Disabled"></i>';
                })
                ->addColumn('actions', function ($user) {
                    return '
                    <button type="button" class="btn btn-sm btn-outline-primary edit-user-btn" 
                            data-id="' . $user->id . '" 
                            data-name="' . $user->name . '" 
                            data-email="' . $user->email . '" 
                            data-telephone="' . $user->telephone . '" 
                            data-role="' . $user->role . '" 
                            data-login-enabled="' . $user->login_enabled . '" 
                            title="Edit" data-bs-toggle="modal" data-bs-target="#editUserModal">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="' . route('users.destroy', $user->id) . '" method="POST" class="d-inline">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Delete this user?\')">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>';
                })
                ->editColumn('last_login', function ($user) {
                    return $user->last_login ? \Carbon\Carbon::parse($user->last_login)->format('Y-m-d H:i') : '-';
                })
                ->rawColumns(['login_status', 'actions'])
                ->make(true);



        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->withErrors(['User not found.']);
        }
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:Admin,Production,Sales,Warehouse',
            'telephone' => 'nullable|digits:10',
            'login_enabled' => 'required|in:0,1',
            'password' => 'nullable|string|min:6',
        ]);

        // Basic fields update
        $user->fill($request->only('name', 'email', 'role', 'telephone', 'login_enabled'));

        // Only update password if admin used the reset toggle
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return back()->with('success', 'User updated successfully.');
    }

}
