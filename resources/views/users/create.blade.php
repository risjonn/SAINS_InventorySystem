@extends('layouts.layout')

@section('title', 'Create New User')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-xl font-semibold mt-4">Create New User</h2>
    <form action="{{ route('users.store') }}" method="POST" class="mt-4 max-w-md">
        @csrf

        <div class="mb-4">
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" name="username" id="username" value="{{ old('username') }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                   required>
            @error('username')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                   required>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}"
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
                    <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
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
                <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                <option value="User Manager" {{ old('role') == 'User Manager' ? 'selected' : '' }}>User Manager</option>
            </select>
            @error('role')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" name="password" id="password"
                   class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                   required>
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                   required>
            @error('password_confirmation')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Create User</button>
        </div>
    </form>
</div>
@endsection
