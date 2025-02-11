@extends('layouts.layout')

@section('title', 'Stock Details')

@section('content')
<div class="container my-5">
    @php
        // Get the selected division value from the query string (default to 'all').
        $selectedDivision = request()->query('division', 'all');
    @endphp

    <h1 class="text-center mb-4">
        Stock Details - {{ $category ?? 'All Products' }}
        @if($selectedDivision !== 'all' && isset($division))
            <br><small class="text-muted">Division: {{ $division->name }}</small>
        @else
            <br><small class="text-muted">All Divisions</small>
        @endif
    </h1>
    <div class="text-center mb-4">
        <a href="{{ route('stocks.overview') }}" class="btn btn-outline-secondary">Back to Overview</a>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        @if($selectedDivision !== 'all' && isset($division))
        <!-- Card Header with Division Name -->
        <div class="card-header bg-light border-0" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
            <h5 class="mb-0 text-center text-muted">{{ $division->name }}</h5>
        </div>
        @endif

        <!-- Card Body with Responsive Table -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
                    <thead style="background-color: #e0e0e0; color: #333;">
                        <tr>
                            <th class="text-center py-3">Serial Number</th>
                            <th class="text-center py-3">Model</th>
                            <th class="text-center py-3">Category</th>
                            @if($selectedDivision === 'all')
                                <th class="text-center py-3">Division</th>
                            @endif
                            <th class="text-center py-3">Remarks</th>
                            <th class="text-center py-3">Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stocks as $stock)
                        <tr style="border-bottom: 1px solid #f0f0f0;">
                            <td class="text-center py-2">{{ $stock->serial_number }}</td>
                            <td class="text-center py-2">{{ $stock->model }}</td>
                            <td class="text-center py-2">{{ $stock->category }}</td>
                            @if($selectedDivision === 'all')
                                <td class="text-center py-2">{{ $stock->division->name }}</td>
                            @endif
                            <td class="text-center py-2">{{ $stock->remarks }}</td>
                            <td class="text-center py-2">{{ \Carbon\Carbon::parse($stock->updated_at)->format('M d, Y h:i A') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
