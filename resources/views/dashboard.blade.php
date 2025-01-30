@extends('layouts.layout')

@section('title', 'Dashboard')

@section('content')
<div class="min-h-screen">
    <h1 class="text-2xl font-bold mb-6">Welcome, {{ auth()->user()->name }}!</h1>
    <p class="text-gray-600 mb-8">Use the side menu to navigate to other sections of the application.</p>

    <!-- Quick Overview Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-sm font-semibold text-gray-500">Total Stocks</h3>
            <p class="mt-2 text-3xl font-bold text-gray-900">42</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-sm font-semibold text-gray-500">Low Stock Items</h3>
            <p class="mt-2 text-3xl font-bold text-gray-900">5</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-sm font-semibold text-gray-500">Users</h3>
            <p class="mt-2 text-3xl font-bold text-gray-900">12</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-sm font-semibold text-gray-500">Recent Updates</h3>
            <p class="mt-2 text-3xl font-bold text-gray-900">8</p>
        </div>
    </div>
</div>
@endsection