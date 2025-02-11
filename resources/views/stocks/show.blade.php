@extends('layouts.layout')

@section('title', 'Stock Details')

@section('content')
<div class="container my-5">
    <h1 class="text-center mb-4">Stock Details - {{ $category ?? 'All Products' }}</h1>
    <div class="text-center mb-4">
        <a href="{{ route('stocks.overview') }}" class="btn btn-outline-secondary">Back to Overview</a>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <!-- Card Header with Division Name -->
        <div class="card-header bg-light border-0" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
            <h5 class="mb-0 text-center text-muted">{{ $division->name ?? 'Division Name' }}</h5>
        </div>
        <!-- Card Body with Responsive Table -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="border-collapse: separate; border-spacing: 0;">
                    <thead style="background-color: #e0e0e0; color: #333;">
                        <tr>
                            <th class="text-center py-3">Serial Number</th>
                            <th class="text-center py-3">Model</th>
                            <th class="text-center py-3">Category</th>
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
                            <td class="text-center py-2">{{ $stock->remarks }}</td>
                            <td class="text-center py-2">{{ $stock->updated_at->format('d-m-Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
