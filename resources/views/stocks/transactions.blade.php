@extends('layouts.layout')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-xl font-semibold mt-4">
        Stock Transactions
        <span class="text-sm ml-2 text-gray-600">
            (Division: {{ isset($division) ? $division->name : 'All Divisions' }})
        </span>
    </h2>

    <table class="w-full border rounded-lg shadow-md bg-white mt-4">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="p-2">Serial Number</th>
                <th class="p-2">Model</th>
                <th class="p-2">Type (IN/OUT)</th>
                <th class="p-2">Handled by</th>
                <th class="p-2">Remarks</th>
                <th class="p-2">Transaction Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr class="border-b">
                <td class="p-2">{{ $transaction->serial_number }}</td>
                <td class="p-2">{{ $transaction->stock_model }}</td>
                <td class="p-2 font-bold {{ $transaction->type == 'IN' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $transaction->type }}
                </td>
                <td class="p-2">{{ $transaction->handled_by_name }}</td>
                <td class="p-2">{{ $transaction->remarks }}</td>
                <td class="p-2">{{ $transaction->transaction_date }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
