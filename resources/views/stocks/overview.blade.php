@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-xl font-semibold mt-4">
        Stock Overview
        @if(isset($division))
            <span class="text-sm ml-2 text-gray-600">(Division: {{ $division->name }})</span>
        @endif
    </h2>

    <!-- Display a message if no stocks are found -->
    @if(session('message'))
        <div class="alert alert-warning mt-4">
            {{ session('message') }}
        </div>
    @endif

    <!-- Stock Overview Table -->
    @if($stocks->isNotEmpty())
    <table class="w-full border rounded-lg shadow-md bg-white mt-4">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="p-2">Serial Number</th>
                <th class="p-2">Model</th>
                <th class="p-2">Category</th>
                <th class="p-2">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stocks as $stock)
            <tr class="border-b">
                <td class="p-2">{{ $stock->serial_number }}</td>
                <td class="p-2">{{ $stock->model }}</td>
                <td class="p-2">{{ $stock->category }}</td>
                <td class="p-2">{{ $stock->remarks }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p>No stocks available for this division.</p>
    @endif
</div>
@endsection
