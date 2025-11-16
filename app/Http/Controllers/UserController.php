<?php

namespace App\Http\Controllers;

use App\Models\LoginAttempt;
use App\Models\User;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    use LogsAdminActivity;
    /**
     * Show activity logs for a specific user (admin actions involving this user)
     */
    public function logs(User $user)
    {
        $logs = \DB::table('admin_activity_logs')
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('metadata', 'like', '%"viewed_user_id":'.$user->id.'%')
                  ->orWhere('metadata', 'like', '%"user_id":'.$user->id.'%');
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('superadmin.users.logs', [
            'user' => $user,
            'logs' => $logs,
        ]);
    }
    public function index(Request $request)
    {
        // Build query with search and filters
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Status filter (active today)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('last_active_at', '>=', now()->startOfDay());
            } elseif ($request->status === 'inactive') {
                $query->where(function($q) {
                    $q->where('last_active_at', '<', now()->startOfDay())
                      ->orWhereNull('last_active_at');
                });
            }
        }

        // Get paginated results
        $users = $query->orderByDesc('last_active_at')->paginate(15);

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

        $this->logActivity('Viewed user account', 'Viewed user account', [
            'viewed_user_id' => $user->id,
            'viewed_user_email' => $user->email
        ]);

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

        $this->logActivity('user_created', "Created user: {$user->name} ({$user->email}) with role {$user->role}", [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
        ]);

        return redirect()->route('users.index')->with('status', 'User created successfully');
    }

    public function edit(User $user)
    {
        // Only super admins can edit other super admins
        if ($user->role === 'super_admin' && auth()->user()->role !== 'super_admin') {
            abort(403, 'You cannot edit a super admin account.');
        }

        $this->logActivity('Accessed user edit', 'Opened edit form for user', [
            'edited_user_id' => $user->id,
            'edited_user_email' => $user->email
        ]);

        return view('superadmin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Only super admins can edit other super admins
        if ($user->role === 'super_admin' && auth()->user()->role !== 'super_admin') {
            abort(403, 'You cannot edit a super admin account.');
        }

        $allowedRoles = ['admin'];
        if (auth()->user()->role === 'super_admin') {
            $allowedRoles[] = 'super_admin';
        }

        $rules = [
            'email' => ['required','email','unique:users,email,'.$user->id],
            'first_name' => ['required','string','max:255'],
            'last_name' => ['required','string','max:255'],
            'username' => ['required','string','max:255','unique:users,username,'.$user->id],
            'role' => ['required','in:' . implode(',', $allowedRoles)],
        ];

        // Only validate password if provided
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Password::defaults()];
        }

        $data = $request->validate($rules);

        // Track changes
        $changes = [];
        $oldValues = [
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'username' => $user->username,
            'role' => $user->role,
        ];

        // Update user data
        $user->email = $data['email'];
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->name = $data['first_name'].' '.$data['last_name'];
        $user->username = $data['username'];
        $user->role = $data['role'];

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = $data['password'];
            $changes[] = 'password';
        }

        // Track what changed
        foreach ($oldValues as $key => $oldValue) {
            if ($user->{$key} !== $oldValue) {
                $changes[] = $key;
            }
        }

        $user->save();

        $this->logActivity('user_updated', "Updated user: {$user->name} ({$user->email})", [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'changes' => $changes,
            'old_values' => $oldValues,
        ]);

        return redirect()->route('users.index')->with('status', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'super_admin') {
            abort(403);
        }

        $userName = $user->name;
        $userEmail = $user->email;
        $userRole = $user->role;

        $user->delete();

        $this->logActivity('user_deleted', "Deleted user: {$userName} ({$userEmail}) with role {$userRole}", [
            'user_id' => $user->id,
            'email' => $userEmail,
            'role' => $userRole,
        ]);

        return redirect()->route('users.index')->with('status', 'User deleted');
    }
}
