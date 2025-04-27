<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\PasswordResetRequest;

class UserManagementController extends Controller
{
    // List all users
    public function index(Request $request)
    {
        $query = User::with('division');
    
        // Live search by username, email, name, or role
        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('role', 'LIKE', "%{$search}%");
            });
        }
    
        // Advanced filter: division
        if ($request->has('division_id') && $request->division_id != "") {
            $query->where('division_id', $request->division_id);
        }
    
        // Advanced filter: role
        if ($request->has('role') && $request->role != "") {
            $query->where('role', $request->role);
        }
    
        $users = $query->orderBy('name')->paginate(10);

        $resetRequests = PasswordResetRequest::with('user')
        ->where('completed', 'Incomplete')
        ->latest()
        ->get();
    
        // If AJAX request, return just the user table partial
        if ($request->ajax()) {
             return view('users.partials.user_table', compact('users'))->render();
        }
    
        // Also pass divisions for the filter dropdown
        $divisions = Division::all();
    
        return view('users.index', compact('users', 'divisions', 'resetRequests'));
    }

    // Show the form to create a new user
    public function create()
    {
        $divisions = Division::all();
        return view('users.create', compact('divisions'));
    }

    // Store the new user
    public function store(Request $request)
    {
        $request->validate([
            'username'   => 'required|unique:users,username',
            'name'       => 'required',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6|confirmed',
            'division_id'=> 'required|exists:divisions,id',
            'role'       => 'required|in:Admin,Super Admin',
        ]);

        User::create([
            'username'   => $request->username,
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'division_id'=> $request->division_id,
            'role'       => $request->role,
        ]);

        return redirect()->route('users.index')->with([
            'success'  => 'User Created Successfully.',
            'resource' => 'user'
        ]);
    }

    // Show the form to edit an existing user
    public function edit(User $user)
    {
        $divisions = Division::all();
        return view('users.edit', compact('user', 'divisions'));
    }

    // Update user details
    public function update(Request $request, User $user)
    {
        $request->validate([
            'username'   => 'required|unique:users,username,' . $user->id,
            'name'       => 'required',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'division_id'=> 'required|exists:divisions,id',
            'role'       => 'required|in:Admin,Super Admin',
        ]);

        $user->update($request->only('username', 'name', 'email', 'division_id', 'role'));

        return redirect()->route('users.index')->with([
            'success' => 'User Updated Successfully',
            'resource' => 'user'
        ]);
    }

    // Delete a user
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with([
            'success' => 'User Removed Successfully.',
            'resource' => 'user'
        ]);
    }

    // Deactive user
    public function deactivate(User $user)
    {
        $user->status = 'Inactive';
        $user->save();
        return redirect()->route('users.index')->with([
            'success' => 'User Deactivated Successfully',
            'resource' => 'user'
        ]);
    }

    // Activate user
    public function activate(User $user)
    {
        $user->status = 'Active';
        $user->save();
        return redirect()->route('users.index')->with([
            'success' => 'User Activated Successfully',
            'resource' => 'user'
        ]);
    }

    // Restore a soft-deleted account
    public function restore($id)
    {
        User::withTrashed()->findOrFail($id)->restore();
        return back()->with('success','User restored.');
    }
}
