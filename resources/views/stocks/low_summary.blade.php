@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4 my-5">
    <!-- Back to Dashboard Button -->
    <div class="my-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-150">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Dashboard
        </a>
    </div>
    <h2 class="text-2xl font-semibold mt-4">Low Stock Summary by Division</h2>

    <div class="mt-4 bg-white rounded-xl shadow-lg p-6">
        <table class="w-full mt-4 border-collapse">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="p-3 border-b">Division</th>
                    <th class="p-3 border-b">Low Stock Items</th>
                    <th class="p-3 border-b">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($divisionsSummary as $division)
                <tr class="border-b odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition-all duration-150 cursor-pointer"
                    onclick="window.location.href='{{ route('stocks.low', ['division' => $division['id']]) }}'">
                    <td class="p-3 border-b">{{ $division['name'] }}</td>
                    <td class="p-3 border-b text-left">
                        <span class="font-bold
                            {{ $division['total'] <= 5 ? 'text-red-600' : ($division['total'] < 10 ? 'text-amber-600' : 'text-green-600') }}">
                            {{ $division['total'] }}
                        </span>
                    </td>
                    <td class="p-3 border-b text-left">
                        <a href="{{ route('stocks.low', ['division' => $division['id']]) }}" class="text-blue-500 hover:text-blue-700 underline">
                            View Details
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
