<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        // List admins only for manage view
        $admins = User::whereIn('role', ['admin'])->orderByDesc('last_active_at')->paginate(10);
        return view('superadmin.users.index', compact('admins'));
    }

    public function create()
    {
        return view('superadmin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required','email','unique:users,email'],
            'first_name' => ['required','string','max:255'],
            'last_name' => ['required','string','max:255'],
            'username' => ['required','string','max:255','unique:users,username'],
            'role' => ['required','in:admin'],
            'password' => ['required','confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'email' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'name' => $data['first_name'].' '.$data['last_name'],
            'username' => $data['username'],
            'role' => 'admin',
            'password' => $data['password'], // cast hashes automatically
        ]);

        return redirect()->route('users.index')->with('status', 'Admin created successfully');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'super_admin') {
            abort(403);
        }
        $user->delete();
        return redirect()->route('users.index')->with('status', 'User deleted');
    }
}
