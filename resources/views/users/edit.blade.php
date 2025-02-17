@extends('layouts.layout')

@section('title', 'Edit User')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-xl font-semibold mt-4">Edit User</h2>
    <form action="{{ route('users.update', $user->id) }}" method="POST" class="mt-4 max-w-md">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                   required>
            @error('username')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                   required>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                   required>
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="division_id" class="block text-sm font-medium text-gray-700">Division</label>
            <select name="division_id" id="division_id"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                    required>
                <option value="">Select Division</option>
                @foreach($divisions as $division)
                    <option value="{{ $division->id }}" {{ old('division_id', $user->division_id) == $division->id ? 'selected' : '' }}>
                        {{ $division->name }}
                    </option>
                @endforeach
            </select>
            @error('division_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
            <select name="role" id="role"
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                    required>
                <option value="">Select Role</option>
                <option value="Admin" {{ old('role', $user->role) == 'Admin' ? 'selected' : '' }}>Admin</option>
                <option value="User Manager" {{ old('role', $user->role) == 'User Manager' ? 'selected' : '' }}>User Manager</option>
            </select>
            @error('role')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Optional: Add password field here if you want to allow a password reset on edit -->
        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update User</button>
        </div>
    </form>
</div>
@endsection
