<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    // List all users
    public function index()
    {
        $users = User::with('division')->get();
        return view('users.index', compact('users'));
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
            'role'       => 'required|in:Admin,User Manager,User', // adjust as needed
        ]);

        User::create([
            'username'   => $request->username,
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'division_id'=> $request->division_id,
            'role'       => $request->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
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
            'role'       => 'required|in:Admin,User Manager,User',
        ]);

        $user->update($request->only('username', 'name', 'email', 'division_id', 'role'));

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    // Delete a user
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
