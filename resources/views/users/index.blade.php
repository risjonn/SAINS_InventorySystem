@extends('layouts.layout')

@section('title', 'Manage Users')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-xl font-semibold mt-4">Manage Users</h2>
    <a href="{{ route('users.create') }}" class="bg-green-600 text-white px-4 py-2 rounded mt-4 inline-block">Add New User</a>
    <table class="min-w-full mt-4 border rounded-lg shadow-md bg-white">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="p-2">Username</th>
                <th class="p-2">Name</th>
                <th class="p-2">Email</th>
                <th class="p-2">Division</th>
                <th class="p-2">Role</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr class="border-b">
                <td class="p-2">{{ $user->username }}</td>
                <td class="p-2">{{ $user->name }}</td>
                <td class="p-2">{{ $user->email }}</td>
                <td class="p-2">{{ $user->division->name ?? 'N/A' }}</td>
                <td class="p-2">{{ $user->role }}</td>
                <td class="p-2">
                    <a href="{{ route('users.edit', $user->id) }}" class="text-blue-500 hover:underline">Edit</a>
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block ml-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
