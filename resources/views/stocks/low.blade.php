@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-2xl font-semibold mt-4">Low Stock Items</h2>
    
    <div class="mt-4 bg-white rounded-xl shadow-lg p-6">
        @if($division)
            <p class="text-lg text-gray-700 mb-2">Division: {{ $division->name }}</p>
        @else
            <p class="text-lg text-gray-700 mb-2">All Divisions</p>
        @endif

        <table class="w-full mt-4 border-collapse">
            <thead>
                <tr class="bg-gray-200 text-gray-800">
                    <th class="p-3 border-b">Category</th>
                    <th class="p-3 border-b">Items Available</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stocks as $stock)
                    <tr class="hover:bg-gray-100 transition-colors">
                        <td class="p-3 border-b">{{ $stock->category }}</td>
                        <td class="p-3 border-b">{{ $stock->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
