<?php

namespace App\Http\Controllers;

use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        // List all users
        $users = User::orderByDesc('last_active_at')->paginate(10);
        return view('superadmin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $loginAttempts = LoginAttempt::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->orderByDesc('attempted_at')
            ->paginate(20);

        $totalAttempts = LoginAttempt::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->count();

        $failedAttempts = LoginAttempt::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->where('successful', false)
            ->count();

        return view('superadmin.users.show', compact('user', 'loginAttempts', 'totalAttempts', 'failedAttempts'));
    }

    public function create()
    {
        return view('superadmin.users.create');
    }

    public function store(Request $request)
    {
        $allowedRoles = ['admin'];
        if (auth()->user()->role === 'super_admin') {
            $allowedRoles[] = 'super_admin';
        }

        $data = $request->validate([
            'email' => ['required','email','unique:users,email'],
            'first_name' => ['required','string','max:255'],
            'last_name' => ['required','string','max:255'],
            'username' => ['required','string','max:255','unique:users,username'],
            'role' => ['required','in:' . implode(',', $allowedRoles)],
            'password' => ['required','confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'email' => $data['email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'name' => $data['first_name'].' '.$data['last_name'],
            'username' => $data['username'],
            'role' => $data['role'],
            'password' => $data['password'], // cast hashes automatically
        ]);

        return redirect()->route('users.index')->with('status', 'User created successfully');
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
