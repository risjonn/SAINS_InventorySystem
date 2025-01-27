@extends('layouts.layout')

@section('title', 'Stock Details')

@section('content')
    <h1 class="text-center mb-4">Stock Details</h1>
    <a href="{{ route('stocks.index') }}" class="btn btn-secondary mb-4">Back to List</a>

    <div class="card mx-auto shadow">
        <div class="card-body">

            <div class="mb-3">
                <label for="serial_number" class="form-label fw-bold">Serial Number</label>
                <div id="serial_number" class="form-control bg-light">{{ $stock->serial_number }}</div>
            </div>

            <div class="mb-3">
                <label for="brand" class="form-label fw-bold">Brand</label>
                <div id="brand" class="form-control bg-light">{{ $stock->brand }}</div>
            </div>

            <div class="mb-3">
                <label for="model" class="form-label fw-bold">Model</label>
                <div id="model" class="form-control bg-light">{{ $stock->model }}</div>
            </div>

            <div class="mb-3">
                <label for="category" class="form-label fw-bold">Category</label>
                <div id="category" class="form-control bg-light">{{ $stock->category }}</div>
            </div>

            <div class="mb-3">
                <label for="quantity" class="form-label fw-bold">Quantity</label>
                <div id="quantity" class="form-control bg-light">{{ $stock->quantity }}</div>
            </div>

            <div class="mb-3">
                <label for="updated_at" class="form-label fw-bold">Updated At</label>
                <div id="updated_at" class="form-control bg-light">{{ $stock->updated_at->format('d-m-Y H:i') }}</div>
            </div>
        </div>
    </div>
@endsection
